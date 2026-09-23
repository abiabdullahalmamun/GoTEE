<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokenLog extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_q_svc_log';
    protected $fillable = ['tokenno','centerId','Date', 'token_svc_no',  'tissuetime', 'ststart', 'ststop','waiting','service','cno','servedby','Qty','e_wait','a_counter','flg','scantime','scan'];

    protected $primaryKey = 'id'; // if this is your PK
    public $incrementing = true;         // or false if not auto-increment
    protected $keyType = 'int';     

    protected $casts = [
        'token_svc_no' => 'integer',
    ];

    /**
     * Get the parent menu.
     */

    public function center()
    {
        return $this->belongsTo(Center::class, 'centerId');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'servedby');
    }
    public function serviceName()
    {
        return $this->belongsTo(Service::class, 'token_svc_no' );
    }
    public function waits()
    {
        return $this->hasMany(TokenLogWait::class, 'svclogId', 'id');
    }
    public function websvc()
    {
        return $this->hasMany(TokenLogWeb::class, 'svclogId', 'id');
    }

}
