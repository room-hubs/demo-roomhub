<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginStoreRequest;
use App\Service\Api\Auth\LoginService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function __construct(
        protected LoginService $loginService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */
    public function store(LoginStoreRequest $request): JsonResponse
    {
        $result = $this->loginService->login($request->validated());

        if (! $result['success']) {
            $status = str_contains($result['message'], 'Too many') ? 429 : 401;

            return response()->json([
                'message' => $result['message']
            ], $status);
        }

        return response()->json([
            'user'         => $result['user'],
            'access_token' => $result['access_token'],
            'token_type'   => $result['token_type'],
            'expires_in'   => $result['expires_in'],
        ])->withHeaders([
            'Cache-Control' => 'no-store',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN LOGIN
    |--------------------------------------------------------------------------
    */
    public function adminLogin(LoginStoreRequest $request): JsonResponse
    {
        $result = $this->loginService->adminLogin($request->validated());

        if (! $result['success']) {
            $status = match (true) {
                str_contains($result['message'], 'Too many') => 429,
                str_contains($result['message'], 'Access denied') => 403,
                default => 401,
            };

            return response()->json([
                'message' => $result['message']
            ], $status);
        }

        return response()->json([
            'user'         => $result['user'],
            'access_token' => $result['access_token'],
            'token_type'   => $result['token_type'],
            'expires_in'   => $result['expires_in'],
        ])->withHeaders([
            'Cache-Control' => 'no-store',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */
    public function logout(Request $request): JsonResponse
    {
        $this->loginService->logout($request->user());

        return response()->json([
            'message' => 'Logged out successfully.'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ME
    |--------------------------------------------------------------------------
    */
    public function me(Request $request): JsonResponse
    {
        return response()->json(
            $this->loginService->me($request->user())
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SOCIAL LOGIN REDIRECTS
    |--------------------------------------------------------------------------
    */
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

    /*
    |--------------------------------------------------------------------------
    | OAUTH CODE EXCHANGE (frontend bridge)
    |--------------------------------------------------------------------------
    */
    public function exchangeOAuthCode(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|size:40'
        ]);

        $cacheKey = "oauth_code:{$request->input('code')}";
        $payload  = Cache::get($cacheKey);

        if (! $payload) {
            return response()->json([
                'message' => 'Invalid or expired code.'
            ], 401);
        }

        Cache::forget($cacheKey);

        return response()->json([
            'user'         => $payload['user'],
            'access_token' => $payload['access_token'],
            'token_type'   => $payload['token_type'],
            'expires_in'   => $payload['expires_in'],
            'needs_role'   => $payload['needs_role'],
        ])->withHeaders([
            'Cache-Control' => 'no-store',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | SOCIAL CALLBACK HELPER
    |--------------------------------------------------------------------------
    */
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
                config('app.frontend_url') .
                '/auth/callback?error=' . urlencode($e->getMessage())
            );
        }

        $code = Str::random(40);

        Cache::put("oauth_code:{$code}", [
            'user'         => $result['user'],
            'access_token' => $result['access_token'],
            'token_type'   => $result['token_type'],
            'expires_in'   => $result['expires_in'],
            'needs_role'   => $result['needs_role'] ?? false,
        ], now()->addSeconds(60));

        return redirect(
            config('app.frontend_url') .
            '/auth/callback?code=' . $code
        );
    }
}
