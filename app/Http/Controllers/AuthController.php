<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Admin;
use App\Models\Donor;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use function Pest\Laravel\withCookie;

class AuthController extends Controller
{

    public function refreshToken(Request $request)
    {
        $oldRefreshTokenValue = $request->cookie("SchoolPlate-refresh_token");
        $userId = Redis::get("refresh_token:$oldRefreshTokenValue");
        // UserId not found meaning Token is expired or deleted,redirect to login
        if (!$userId) {
            return response()->json(
                status: 401,
                data: ['message' => 'Unauthorized Access']
            );
        }

        $user = User::find($userId);
        if (!$user) {
            return response()->json(
                status: 404,
                data: ['message' => 'User not found']
            );
        }

        $newAccessToken = $user->createToken('_accessToken', expiresAt: now()->addMinutes(config('sanctum.expiration')))->plainTextToken;
        $newRefreshToken = Str::random(64);
        Redis::del("refresh_token:$oldRefreshTokenValue");
        Redis::setex("refresh_token:$newRefreshToken", config('sanctum.rt_expiration'), $user->id);
        return response()->json(
            data: [
                'user' => $user,
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
            return response()->json(
                status: 404,
                data: ['status' => 'error', 'message' => 'Invalid Credentials']
            );
        }
        $accessToken = $user->createToken('_accessToken')->plainTextToken;
        $refreshToken = Str::random(64);
        Redis::setex("refresh_token:$refreshToken", config('sanctum.rt_expiration'), $user->id);
        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'user' => $user,
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
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully.'
        ]);
    }


    public function register(UserRequest $request): JsonResponse
    {
        try {
            $role = $request->role;
            $validated = $request->validated();

            switch ($role) {
                case 'student':
                    $user = Student::create($validated);
                    break;
                case 'donor':
                   $user = Donor::create($validated);
                    break;
                case 'admin':
                    $user = Admin::create($validated);
            
                    break;
                default:
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid role'
                    ], 422);
            }


            $accessToken = $user->createToken('_accessToken')->plainTextToken;
            $accessToken = $user->createToken('_accessToken')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Registration successful',
                'user' => $user,
                'token' => $accessToken,
            ], 201);


        } catch (QueryException $e) {
            // Database error
            if ($e->getCode() === '23000') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Phone number is already taken.',
                ], 422);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            // Any other unexpected error
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

}
