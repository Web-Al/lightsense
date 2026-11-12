<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLES = [
        'admin' => 'Administrator',
        'staff' => 'Staff',
        'viewer' => 'Viewer',
    ];

    // Higher number = more access
    public const LEVELS = ['viewer' => 1, 'staff' => 2, 'admin' => 3];

    protected $fillable = ['name', 'email', 'password', 'role'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // True if the user's role is the given role or higher
    public function hasRole(string $minimum): bool
    {
        return (self::LEVELS[$this->role] ?? 0) >= self::LEVELS[$minimum];
    }

    public function isStaff(): bool
    {
        return $this->hasRole('staff');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function getRoleLabelAttribute(): string
    {
        return self::ROLES[$this->role] ?? 'Viewer';
    }
}
