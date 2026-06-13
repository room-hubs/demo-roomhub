<?php

namespace App\Service\Api\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class RoleService
{
    private const ALLOWED_ROLES = ['landlord', 'tenant', 'rental'];
    // update role
    public function updateRole(User $user, string $role): array
    {
        if (! in_array($role, self::ALLOWED_ROLES)) {
            return [
                'success' => false, 
                'message' => 'Invalid role selected.',
            ];
        }

        // If role already assigned, return current state
        if ($user->getRoleNames()->isNotEmpty()) {
            $user = $user->refresh()->load('roles');

            return [
                'success' => true,
                'message' => 'Role already assigned.',
                'user'    => $this->formatUser($user),
            ];
        }

        // Assign role, no need to update status, user is already active
        $user->assignRole($role);

        $user = $user->refresh()->load('roles');

        Log::info('Role assigned', [
            'user_id' => $user->id,
            'role'    => $role,
            'ip'      => request()->ip(),
        ]);

        return [
            'success' => true,
            'message' => 'Role assigned successfully.',
            'user'    => $this->formatUser($user),
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