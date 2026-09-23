<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_sms_log';
    protected $fillable = ['Date',  'centerId','type', 'contact', 'text','lang', 'webref','try','created_by'];

    /**
     * Get the parent menu.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function center()
    {
        return $this->belongsTo(Center::class, 'centerId');
    }
    public function web()
    {
        return $this->belongsTo(AppReceive::class, 'webref');
    }
   
}
