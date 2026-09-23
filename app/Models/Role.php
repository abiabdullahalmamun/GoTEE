<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use Loggable;
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    // Relationship: A role can have many users
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Relationship: A role can have many permissions for menus
    public function permissions()
    {
        return $this->hasMany(RolePermission::class);
    }
}
