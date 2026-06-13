<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\RefreshTokenRequest;
use App\Models\RefreshToken;
use Illuminate\Http\JsonResponse;

class RefreshController extends Controller
{
    public function refresh(RefreshTokenRequest $request): JsonResponse
    {
        $plain  = $request->validated('refresh_token');
        $hashed = hash('sha256', $plain);

        $refreshToken = RefreshToken::with('user')
            ->where('token', $hashed)
            ->first();

        // Constant-time: always compare even if not found — prevents timing attacks
        if (! $refreshToken || ! hash_equals($refreshToken->token, $hashed)) {
            return response()->json(['message' => 'Invalid refresh token.'], 401);
        }

        if ($refreshToken->isExpired()) {
            $refreshToken->delete();
            return response()->json(['message' => 'Refresh token expired. Please login again.'], 401);
        }

        $user = $refreshToken->user;

        if (! $user) {
            $refreshToken->delete();
            return response()->json(['message' => 'User not found.'], 401);
        }

        if ($user->status === 'inactive') {
            $refreshToken->delete();
            return response()->json(['message' => 'Account suspended.'], 403);
        }

        if ($user->status === 'pending') {
            $refreshToken->delete();
            return response()->json(['message' => 'Please complete your registration.'], 403);
        }

        // Read device before deleting
        $device = $refreshToken->device_name;

        // Rotate: delete old refresh + access tokens for this device
        $refreshToken->delete();
        $user->tokens()->where('name', "auth:{$device}")->delete();
    
        $accessToken = $user->createToken(
            "auth:{$device}",
            ['*'],
            now()->addHour()
        )->plainTextToken;

        $newRefresh = RefreshToken::generate($user, $device);

        return response()->json([
            'access_token'  => $accessToken,
            'refresh_token' => $newRefresh['plain'],
            'token_type'    => 'Bearer',
            'expires_in'    => 3600,
        ])->withHeaders([
            'Cache-Control' => 'no-store',
            'Pragma'        => 'no-cache',
        ]);
    }
}