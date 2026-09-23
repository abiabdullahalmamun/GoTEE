<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserConnection extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'connected_user_id', 'created_by'];


    protected $hidden = [
        'created_at',
        'updated_at'
    ];


}
