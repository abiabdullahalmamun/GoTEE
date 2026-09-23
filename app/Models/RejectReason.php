<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RejectReason extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_reject_reason';
    protected $fillable = ['reason_name', 'status','created_by'];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    
    // // Relationship: Permissions
    // public function permissions()
    // {
    //     return $this->hasMany(RolePermission::class, 'menu_id');
    // }
}
