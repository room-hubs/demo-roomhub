<?php

namespace App\Service\Api\Auth;

use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RegisterService
{
    // registe function
    public function register(array $data): array
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email']    ?? null,
            'phone'    => $data['phone']    ?? null,
            'password' => Hash::make($data['password']),
            'status'   => 'active', // activated after role assignment
        ]);

        Log::info('New user registered', [
            'user_id' => $user->id,
            'ip'      => request()->ip(),
        ]);

        // Issue both access + refresh tokens 
        $tokens = $this->issueSetupTokens($user);

        return [
            'success'       => true,
            'message'       => 'Registration successful. Please select your role.',
            'user'          => $this->formatUser($user),
            'token'         => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
            'needs_role'    => true,
        ];
    }

    // ISSUE SETUP TOKENS
    // Short-lived access token + refresh token for new users    
    private function issueSetupTokens(User $user): array
    {
        $device = request()->input('device_name', 'web');

        // Short-lived setup token — expires in 1 hour
        $accessToken = $user->createToken(
            "setup:{$device}", ['*'], now()->addHour()
        )->plainTextToken;

        // Issue refresh token so silent refresh works after registration
        $refresh = RefreshToken::generate($user, $device);

        return [
            'access_token'  => $accessToken,
            'refresh_token' => $refresh['plain'],
        ];
    }
    // formate user
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
}