<?php

namespace App\Models;

use App\Models\SocialAccount;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected string $guard_name = 'sanctum';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'avatar',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {   
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ROLE CHECKS
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isLandlord(): bool
    {
        return $this->hasRole('landlord');
    }

    public function isRental(): bool
    {
        return $this->hasRole('rental');
    }

    // SOCIAL ACCOUNTS
    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    // STATUS CHECKS
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
