<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisplayScroll extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_q_displayScroll';

    protected $fillable =['centerId','Date', 'tokenno','counterNo','svc_no'];

    protected $primaryKey = 'id'; // if this is your PK
    public $incrementing = true;         // or false if not auto-increment
    protected $keyType = 'int'; 


    public function center()
    {
        return $this->belongsTo(Center::class, 'centerId');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'svc_no');
    }


}
