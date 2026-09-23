<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'code', 'created_by', 'updated_by'];

    public function users()
    {
        return $this->hasMany(User::class,'user_type_id');
    }

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
