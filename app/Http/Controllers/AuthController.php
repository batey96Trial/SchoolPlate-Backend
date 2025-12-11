<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResourceFactory;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function refreshToken(Request $request)
    {
        $oldRefreshTokenValue = $request->cookie("SchoolPlate-refresh_token");
        $userId = Redis::get("refresh_token:$oldRefreshTokenValue");
        if (!$userId) {
            throw new AuthenticationException();
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


    public function login(LoginRequest $request): JsonResponse
    {
        $request->validated();
        $user = User::where('telephone', $request->telephone)->firstOrFail();
        if (!Hash::check($request->password, $user->password)) {
            // Hash:check() doesn't throw error,we handle it ourself
            throw ValidationException::withMessages([
                'status' => 'error',
                'message' => 'Invalid Password'
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


    public function register(RegisterRequest $request): JsonResponse
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
