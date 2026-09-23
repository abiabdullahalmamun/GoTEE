<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Bureau;
use App\Traits\Loggable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
   
    protected $fillable = [
        'name',
         'FullName',
        'role_id',
        'email',
        'phone',
        'password',
        'address',
        'is_approve',
        'login_attempts',
        'last_login_at',
        'last_login_ip',
        'is_active',
        'created_by',
        'updated_by',
        'centerId'

    ];

    use  HasApiTokens, HasFactory, Notifiable, Loggable;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the parent menu.
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function center()
    {
        return $this->belongsTo(Center::class, 'centerId');
    }

    public function stickerPrintLogs()
    {
        return $this->hasMany(StickerPrintLog::class, 'created_by', 'id');
    }
    public function appReceive()
    {
        return $this->hasMany(AppReceive::class, 'created_by', 'id');
    }
    public function Override()
    {
        return $this->hasMany(AptOverride::class, 'created_by', 'id');
    }
    public function appaction()
    {
        return $this->hasMany(AppLog::class, 'created_by', 'id');
    }
    public function hasPermission($permission)
    {
        // Load role and permissions relationship
        $role = $this->role()->with('permissions')->first();

        if (!$role) {
            return false;
        }

        // Assuming `RolePermission` model has a `permission_name` or `slug` field
        return $role->permissions->contains('slug', $permission);
    }


}
