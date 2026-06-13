<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'sanctum',
        ]);

        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'     => 'Admin User',
                'phone'    => null,
                'password' => Hash::make('123456Dam'),
                'status'   => 'active',
            ]
        );

        $admin->syncRoles(['admin']);
    }
}