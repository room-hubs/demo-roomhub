<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginStoreRequest;
use App\Models\RefreshToken;
use App\Service\Api\Auth\LoginService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function __construct(protected LoginService $loginService) {}
    // lgoin controller
    public function store(LoginStoreRequest $request): JsonResponse
    {
        $result = $this->loginService->login($request->validated());

        if (! $result['success']) {
            $status = str_contains($result['message'], 'Too many') ? 429 : 401;
            return response()->json(['message' => $result['message']], $status);
        }

        return response()->json([
            'user'          => $result['user'],
            'access_token'  => $result['access_token'],
            'refresh_token' => $result['refresh_token'],
            'token_type'    => $result['token_type'],
            'expires_in'    => $result['expires_in'],
        ])->withHeaders(['Cache-Control' => 'no-store']);
    }
    // admin login controller
    public function adminLogin(LoginStoreRequest $request): JsonResponse
    {
        $result = $this->loginService->adminLogin($request->validated());

        if (! $result['success']) {
            $status = match (true) {
                str_contains($result['message'], 'Too many')     => 429,
                str_contains($result['message'], 'Access denied') => 403,
                default                                           => 401,
            };
            return response()->json(['message' => $result['message']], $status);
        }

        return response()->json([
            'user'          => $result['user'],
            'access_token'  => $result['access_token'],
            'refresh_token' => $result['refresh_token'],
            'token_type'    => $result['token_type'],
            'expires_in'    => $result['expires_in'],
        ])->withHeaders(['Cache-Control' => 'no-store']);
    }
    // refresh token 
    public function refresh(Request $request): JsonResponse
    {
        $request->validate(['refresh_token' => 'required|string']);

        $plain        = $request->input('refresh_token');
        $hashed       = hash('sha256', $plain);
        $refreshToken = RefreshToken::with('user')->where('token', $hashed)->first();

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

        $device = $refreshToken->device_name;

        // Rotate: delete old tokens
        $refreshToken->delete();
        $user->tokens()->where('name', "auth:{$device}")->delete();

        // Issue new pair
        $accessToken = $user->createToken(
            "auth:{$device}", ['*'], now()->addHour()
        )->plainTextToken;

        $newRefresh = RefreshToken::generate($user, $device);

        return response()->json([
            'access_token'  => $accessToken,
            'refresh_token' => $newRefresh['plain'],
            'token_type'    => 'Bearer',
            'expires_in'    => 3600,
        ])->withHeaders(['Cache-Control' => 'no-store']);
    }
    // logout
    public function logout(Request $request): JsonResponse
    {
        $this->loginService->logout($request->user());
        return response()->json(['message' => 'Logged out successfully.']);
    }
    // admin
    public function me(Request $request): JsonResponse
    {
        return response()->json($this->loginService->me($request->user()));
    }
    // google 
    public function redirectGoogle()
    {
        return $this->loginService->redirectGoogle();
    }

    public function googleCallback()
    {
        return $this->handleSocialCallbackRedirect(
            fn () => $this->loginService->handleGoogleCallback()
        );
    }
    // facebook
    public function redirectFacebook()
    {
        return $this->loginService->redirectFacebook();
    }

    public function facebookCallback()
    {
        return $this->handleSocialCallbackRedirect(
            fn () => $this->loginService->handleFacebookCallback()
        );
    }

    // OAUTH CODE EXCHANGE    
    public function exchangeOAuthCode(Request $request): JsonResponse
    {
        $request->validate(['code' => 'required|string|size:40']);

        $cacheKey = "oauth_code:{$request->input('code')}";
        $payload  = Cache::get($cacheKey);

        if (! $payload) {
            return response()->json(['message' => 'Invalid or expired code.'], 401);
        }

        Cache::forget($cacheKey);

        return response()->json([
            'user'          => $payload['user'],
            'access_token'  => $payload['access_token'],
            'refresh_token' => $payload['refresh_token'],
            'token_type'    => $payload['token_type'],
            'expires_in'    => $payload['expires_in'],
            'needs_role'    => $payload['needs_role'],
        ])->withHeaders(['Cache-Control' => 'no-store']);
    }

    // HELPER — social callback redirect
    private function handleSocialCallbackRedirect(callable $callback)
    {
        try {
            $result = $callback();
        } catch (\Throwable $e) {
            Log::error('Social login failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return redirect(
                config('app.frontend_url') . '/auth/callback'
                . '?error=' . urlencode($e->getMessage())
            );
        }

        $code = Str::random(40);

        Cache::put("oauth_code:{$code}", [
            'user'          => $result['user'],
            'access_token'  => $result['access_token'],
            'refresh_token' => $result['refresh_token'],
            'token_type'    => $result['token_type'],
            'expires_in'    => $result['expires_in'],
            'needs_role'    => $result['needs_role'],
        ], now()->addSeconds(60));

        return redirect(
            config('app.frontend_url') . '/auth/callback'
            . '?code=' . $code
        );
    }
}