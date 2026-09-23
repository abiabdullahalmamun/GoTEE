<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsOtp extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_sms_otp';
    protected $fillable = ['Date',  'centerId', 'contact', 'otp'];

    /**
     * Get the parent menu.
     */

    public function center()
    {
        return $this->belongsTo(Center::class, 'centerId');
    }
   
}
