<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasPermission($permission)
    {
        if (!$this->role) {
            return false;
        }
        
        // Super Admin has all permissions (assuming 'super-admin' slug)
        if ($this->role->slug === 'super-admin') {
            return true;
        }

        $permissions = $this->role->permissions ?? [];
        return in_array($permission, $permissions);
    }

    public function hasRole($roleSlug)
    {
        return $this->role && $this->role->slug === $roleSlug;
    }
}
