<?php

namespace App\Service\Api\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RegisterService
{
    /*
    |--------------------------------------------------------------------------
    | REGISTER USER (SPATIE READY)
    |--------------------------------------------------------------------------
    */
    public function register(array $data, string $ip, string $device): array
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'] ?? null,
            'phone'    => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'status'   => 'active',
        ]);

        Log::info('New user registered', [
            'user_id' => $user->id,
            'ip'      => $ip,
        ]);

        /*
        |--------------------------------------------------------------------------
        | DEFAULT ROLE (OPTIONAL)
        |--------------------------------------------------------------------------
        | If you want every new user to be "rental" by default:
        */
        $user->assignRole('rental');

        $token = $this->issueSetupToken($user, $device);

        return [
            'success' => true,
            'message' => 'Registration successful.',
            'user'    => $this->formatUser($user->refresh()),
            ...$token,
            'needs_role' => false, // because role is already assigned
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | SANCTUM TOKEN (NO REFRESH TOKEN SYSTEM)
    |--------------------------------------------------------------------------
    */
    private function issueSetupToken(User $user, string $device = 'web'): array
    {
        $user->tokens()->where('name', "setup:{$device}")->delete();

        $accessToken = $user->createToken(
            "setup:{$device}",
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
    | FORMAT USER (SPATIE ROLES)
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
}
