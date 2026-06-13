<?php

namespace App\Service\Api\Auth;

use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Socialite\Facades\Socialite;

class LoginService
{
    // login function for general user like rental or landlord
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

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password ?? Hash::make('dummy'))) {
            RateLimiter::hit($key, 60);

            Log::warning('Failed login attempt', [
                'email' => $data['email'],
                'ip'    => request()->ip(),
            ]);

            return ['success' => false, 'message' => 'Invalid credentials.'];
        }

        if ($user->status === 'inactive') {
            return ['success' => false, 'message' => 'Account suspended.'];
        }

        if ($user->status === 'pending') {
            return ['success' => false, 'message' => 'Please complete your registration.'];
        }

        RateLimiter::clear($key);

        $user->refresh()->load('roles');

        Log::info('Login success', ['user_id' => $user->id, 'ip' => request()->ip()]);

        return [
            'success' => true,
            'user'    => $this->formatUser($user),
            ...$this->generateToken($user),
        ];
    }

    // form login for admin only
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

        $user = User::where('email', $data['email'])->with('roles')->first();

        if (! $user || ! Hash::check($data['password'], $user->password ?? Hash::make('dummy'))) {
            RateLimiter::hit($key, 60);

            Log::warning('Failed admin login attempt', [
                'email' => $data['email'],
                'ip'    => request()->ip(),
            ]);

            return ['success' => false, 'message' => 'Invalid credentials.'];
        }

        if ($user->status === 'inactive') {
            return ['success' => false, 'message' => 'Account suspended.'];
        }

        if (! $user->hasRole('admin')) {
            Log::warning('Unauthorized admin login attempt', [
                'user_id' => $user->id,
                'ip'      => request()->ip(),
            ]);

            return ['success' => false, 'message' => 'Access denied. Admin only.'];
        }

        RateLimiter::clear($key);

        Log::info('Admin login success', ['user_id' => $user->id, 'ip' => request()->ip()]);

        return [
            'success' => true,
            'user'    => $this->formatUser($user),
            ...$this->generateToken($user, 'admin'),
        ];
    }
    // function for logout
    public function logout(User $user): void
    {
        $device  = $this->resolveDevice();
        $context = $this->resolveContext($user);

        $user->tokens()->where('name', "{$context}:{$device}")->delete();
        $user->refreshTokens()->where('device_name', $device)->delete();
    }
    // default for admin
    public function me(User $user): array
    {
        return $this->formatUser($user->refresh()->load('roles'));
    }
    // google
    public function redirectGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback(): array
    {
        return $this->handleSocialCallback('google');
    }   
    // facebook
    public function redirectFacebook()
    {
        return Socialite::driver('facebook')->stateless()->redirect();
    }

    public function handleFacebookCallback(): array
    {
        return $this->handleSocialCallback('facebook');
    }
    // social login
    private function handleSocialCallback(string $driver): array
    {
        $socialUser = Socialite::driver($driver)->stateless()->user();

        $user = User::where('email', $socialUser->getEmail())->first();

        if (! $user) {
            $user = User::create([
                'provider'    => $driver,
                'provider_id' => $socialUser->getId(),
                'name'        => $socialUser->getName(),
                'email'       => $socialUser->getEmail(),
                'avatar'      => $socialUser->getAvatar(),
                'status'      => 'pending',
            ]);
        } else {
            $user->update([
                'provider'    => $driver,
                'provider_id' => $socialUser->getId(),
                'avatar'      => $socialUser->getAvatar() ?? $user->avatar,
            ]);
        }

        if ($user->status === 'inactive') {
            Log::warning('Suspended user attempted social login', [
                'driver'  => $driver,
                'user_id' => $user->id,
                'ip'      => request()->ip(),
            ]);

            throw new \Exception('Account suspended.', 403);
        }

        $user      = $user->refresh()->load('roles');
        $needsRole = $user->getRoleNames()->isEmpty();
        $context   = $needsRole ? 'setup' : 'auth';

        Log::info('Social login success', [
            'driver'     => $driver,
            'user_id'    => $user->id,
            'needs_role' => $needsRole,
            'ip'         => request()->ip(),
        ]);

        return [
            'success'    => true,
            'user'       => $this->formatUser($user),
            'needs_role' => $needsRole,
            ...$this->generateToken($user, $context),
        ];
    }
    // generate token
    private function generateToken(User $user, string $context = 'auth'): array
    {
        $device = $this->resolveDevice();

        $user->tokens()->where('name', "{$context}:{$device}")->delete();
        $user->refreshTokens()->where('device_name', $device)->delete();

        $accessToken = $user->createToken(
            "{$context}:{$device}", ['*'], now()->addHour()
        )->plainTextToken;

        $refreshToken = RefreshToken::generate($user, $device);

        return [
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken['plain'],
            'token_type'    => 'Bearer',
            'expires_in'    => 3600,
        ];
    }
    // formate user
    private function formatUser(User $user): array
    {
        $user->loadMissing('roles');
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
    // helpers
    private function resolveDevice(): string
    {
        $device = request()->input('device_name', 'web');
        return substr(preg_replace('/[^\x20-\x7E]/', '', $device), 0, 64);
    }

    private function resolveContext(User $user): string
    {
        $name  = $user->currentAccessToken()?->name ?? '';
        $parts = explode(':', $name, 2);
        return $parts[0] ?? 'auth';
    }
}