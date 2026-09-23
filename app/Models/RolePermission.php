<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    use Loggable;
    use HasFactory;

    protected $fillable = ['role_id', 'menu_id', 'can_view', 'can_create', 'can_edit', 'can_delete'];

    // Relationship: Role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Relationship: Menu
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
