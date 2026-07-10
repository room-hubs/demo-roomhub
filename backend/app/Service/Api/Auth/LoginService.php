<?php

namespace App\Service\Api\Auth;

use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Socialite\Facades\Socialite;

class LoginService
{
    /*
    |--------------------------------------------------------------------------
    | LOGIN (email OR phone)
    |--------------------------------------------------------------------------
    */
    public function login(array $data): array
    {
        $key = 'login:' . strtolower($data['email']) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return [
                'success' => false,
                'message' => "Too many login attempts. Try again in {$seconds} seconds.",
            ];
        }

        // email OR phone login
        $user = User::where('email', $data['email'])
            ->orWhere('phone', $data['email'])
            ->first();

        if (
            ! $user ||
            ! $user->password ||
            ! Hash::check($data['password'], $user->password)
        ) {
            RateLimiter::hit($key, 60);

            Log::warning('Failed login attempt', [
                'login' => $data['email'],
                'ip'    => request()->ip(),
            ]);

            return [
                'success' => false,
                'message' => 'Invalid credentials.',
            ];
        }

        // status check (schema aligned)
        if ($user->status === 'inactive') {
            return [
                'success' => false,
                'message' => 'Account inactive.',
            ];
        }

        if ($user->status === 'suspended') {
            return [
                'success' => false,
                'message' => 'Account suspended.',
            ];
        }

        RateLimiter::clear($key);

        $user->refresh()->load('roles');

        Log::info('Login success', [
            'user_id' => $user->id,
            'ip'      => request()->ip(),
        ]);

        return [
            'success' => true,
            'user'    => $this->formatUser($user),
            ...$this->generateToken($user),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN LOGIN
    |--------------------------------------------------------------------------
    */
    public function adminLogin(array $data): array
    {
        $key = 'admin-login:' . strtolower($data['email']) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return [
                'success' => false,
                'message' => "Too many attempts. Try again in {$seconds} seconds.",
            ];
        }

        $user = User::where('email', $data['email'])
            ->orWhere('phone', $data['email'])
            ->with('roles')
            ->first();

        if (
            ! $user ||
            ! $user->password ||
            ! Hash::check($data['password'], $user->password)
        ) {
            RateLimiter::hit($key, 60);

            Log::warning('Failed admin login attempt', [
                'login' => $data['email'],
                'ip'    => request()->ip(),
            ]);

            return [
                'success' => false,
                'message' => 'Invalid credentials.',
            ];
        }

        if (in_array($user->status, ['inactive', 'suspended'])) {
            return [
                'success' => false,
                'message' => 'Account disabled.',
            ];
        }

        if (! $user->hasRole('admin')) {
            Log::warning('Unauthorized admin login attempt', [
                'user_id' => $user->id,
                'ip'      => request()->ip(),
            ]);

            return [
                'success' => false,
                'message' => 'Access denied. Admin only.',
            ];
        }

        RateLimiter::clear($key);

        Log::info('Admin login success', [
            'user_id' => $user->id,
            'ip'      => request()->ip(),
        ]);

        return [
            'success' => true,
            'user'    => $this->formatUser($user),
            ...$this->generateToken($user, 'admin'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | LOGOUT (Sanctum)
    |--------------------------------------------------------------------------
    */
    public function logout(User $user): void
    {
        $device  = $this->resolveDevice();
        $context = $this->resolveContext($user);

        // delete only current device token
        $user->tokens()->where('name', "{$context}:{$device}")->delete();
    }

    /*
    |--------------------------------------------------------------------------
    | CURRENT USER
    |--------------------------------------------------------------------------
    */
    public function me(User $user): array
    {
        return $this->formatUser($user->refresh()->load('roles'));
    }

    /*
    |--------------------------------------------------------------------------
    | SOCIAL LOGIN (GOOGLE / FACEBOOK)
    |--------------------------------------------------------------------------
    */
    public function redirectGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback(): array
    {
        return $this->handleSocialCallback('google');
    }

    public function redirectFacebook()
    {
        return Socialite::driver('facebook')->stateless()->redirect();
    }

    public function handleFacebookCallback(): array
    {
        return $this->handleSocialCallback('facebook');
    }

    private function handleSocialCallback(string $driver): array
    {
        $socialUser = Socialite::driver($driver)->stateless()->user();

        $email = $socialUser->getEmail();

        $socialAccount = SocialAccount::where('provider', $driver)
            ->where('provider_id', $socialUser->getId())
            ->first();

        $user = $socialAccount?->user;

        if (! $user && $email) {
            $user = User::where('email', $email)->first();
        }

        if (! $user) {
            $user = User::create([
                'name'   => $socialUser->getName(),
                'email'  => $email,
                'avatar' => $socialUser->getAvatar(),
                'status' => 'active',
            ]);
        } elseif ($socialUser->getAvatar() && $user->avatar !== $socialUser->getAvatar()) {
            $user->update(['avatar' => $socialUser->getAvatar()]);
        }

        SocialAccount::updateOrCreate([
            'provider'    => $driver,
            'provider_id' => $socialUser->getId(),
        ], [
            'user_id'        => $user->id,
            'access_token'   => $socialUser->token ?? null,
            'refresh_token'  => $socialUser->refreshToken ?? null,
            'token_expire_at'=> $socialUser->expiresIn ? now()->addSeconds($socialUser->expiresIn) : null,
        ]);

        if (in_array($user->status, ['inactive', 'suspended'])) {
            return [
                'success' => false,
                'message' => 'Account disabled.',
            ];
        }

        $user = $user->refresh()->load('roles');

        $needsRole = $user->getRoleNames()->isEmpty();
        $context   = $needsRole ? 'setup' : 'auth';

        return [
            'success'    => true,
            'user'       => $this->formatUser($user),
            'needs_role' => $needsRole,
            ...$this->generateToken($user, $context),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | SANCTUM TOKEN GENERATION
    |--------------------------------------------------------------------------
    */
    private function generateToken(User $user, string $context = 'auth'): array
    {
        $device = $this->resolveDevice();

        // remove old token for same device/context
        $user->tokens()->where('name', "{$context}:{$device}")->delete();

        $accessToken = $user->createToken(
            "{$context}:{$device}",
            ['*'],
            now()->addHour()
        )->plainTextToken;

        return [
            'access_token' => $accessToken,
            'token_type'   => 'Bearer',
            'expires_in'   => 3600,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT USER
    |--------------------------------------------------------------------------
    */
    private function formatUser(User $user): array
    {
        $roles = $user->getRoleNames();

        return [
            'id'     => $user->id,
            'name'   => $user->name,
            'email'  => $user->email,
            'phone'  => $user->phone,
            'avatar' => $user->avatar,
            'status' => $user->status,
            'role'   => $roles->first(),
            'roles'  => $roles,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */
    private function resolveDevice(): string
    {
        $device = request()->input('device_name', 'web');

        return substr(
            preg_replace('/[^\x20-\x7E]/', '', $device),
            0,
            64
        );
    }

    private function resolveContext(User $user): string
    {
        $name  = $user->currentAccessToken()?->name ?? '';
        $parts = explode(':', $name, 2);

        return $parts[0] ?? 'auth';
    }
}
