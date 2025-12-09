<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResourceFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use function Pest\Laravel\withCookie;

class AuthController extends Controller
{

    public function refreshToken(Request $request)
    {
        $oldRefreshTokenValue = $request->cookie("SchoolPlate-refresh_token");
        $userId = Redis::get("refresh_token:$oldRefreshTokenValue");
        if (!$userId) {
            return response()->json(
                status: 401,
                data: [
                        'status' => 'error',
                        'message' => 'Unauthenticated'
                    ]
            );
        }

        $user = User::findOrFail($userId);
        $newAccessToken = $user->createToken('auth-tokens', expiresAt: now()->addMinutes(config('sanctum.expiration')))->plainTextToken;
        $newRefreshToken = Str::random(64);
        Redis::del("refresh_token:$oldRefreshTokenValue");
        Redis::setex("refresh_token:$newRefreshToken", config('sanctum.rt_expiration'), $user->id);
        return response()->json(
            data: [
                'user' => UserResourceFactory::produce($user),
                'token' => $newAccessToken
            ]
        )->withCookie(
                cookie: cookie(
                    name: config('sanctum.token_prefix') . 'refresh_token',
                    value: $newRefreshToken,
                    minutes: intdiv(config('sanctum.rt_expiration'), 60),
                    secure: true,
                    httpOnly: true
                )
            );
    }


    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'telephone' => ['required', 'regex:/^(?:\+237)?[26][0-9]{8}$/'],
            'password' => ['required'],
        ]);
        $user = User::where('telephone', $request->telephone)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            // manualy throw validation exception- refer to app.php for global state
            throw ValidationException::withMessages([
                'status' => 'error',
                'message' => 'Invalid Credentials'
            ]);
        }
        $user->tokens()->where('name', 'auth-token')->delete();
        $accessToken = $user->createToken('auth-token', expiresAt: now()->addMinutes(config('sanctum.expiration')))->plainTextToken;
        $refreshToken = Str::random(64);
        Redis::setex("refresh_token:$refreshToken", config('sanctum.rt_expiration'), $user->id);
        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'user' => UserResourceFactory::produce($user),
            'token' => $accessToken,
        ])->withCookie(
                cookie: cookie(
                    name: config('sanctum.token_prefix') . 'refresh_token',
                    value: $refreshToken,
                    minutes: intdiv(config('sanctum.rt_expiration'), 60),
                    secure: true,
                    httpOnly: true
                )
            );


    }

    public function logout(Request $request)
    {
        $refreshToken = $request->cookie("SchoolPlate-refresh_token");
        Redis::del("refresh_token:$refreshToken");
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully.'
        ]);
    }


    public function register(UserRequest $request): JsonResponse
    {

        $validated = $request->validated();

        /** @var User $user */
        $user = DB::transaction(callback: fn(): User => User::create($validated));

        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful',
            'user' => UserResourceFactory::produce($user),
        ], 201);
    }

}
