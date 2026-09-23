<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersRegistrationRequest extends Model
{
    use Loggable;
    use HasFactory;

    protected $fillable = [
        'details',
        'is_active',
        'otp',
        'otp_verified_at',
        'created_by',
        'updated_by',
    ];
}
