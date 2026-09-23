<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'role',
        'department',
        'job_title',
        'phone',
        'status',
        'password',
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

    public function hasPermission(string $permission): bool
    {
        if ($this->role === 'admin') return true;

        $configured = RolePermission::where('role', $this->role)->value('permissions');
        if ($configured !== null) return in_array($permission, (array) $configured, true);

        return in_array($permission, match ($this->role) {
            'manager' => ['view_dashboard', 'manage_staff', 'manage_tours', 'manage_bookings', 'manage_partners', 'manage_marketing', 'manage_documents', 'view_reports'],
            'agent' => ['view_dashboard', 'manage_bookings', 'manage_documents'],
            default => ['view_dashboard', 'manage_bookings'],
        }, true);
    }
}
