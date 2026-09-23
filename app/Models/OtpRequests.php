<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpRequests extends Model
{
    use HasFactory;
    protected $fillable = ['phone', 'otp', 'otp_expired_at', 'request_data','verified_at','created_at'];
    public $timestamps = false;

    protected $hidden = [
        'created_at'
    ];
}
