<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokenLogWait extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_q_svc_wait';
    protected $fillable = ['svclogId','wait_start','wait_end', 'difference'];

    protected $primaryKey = 'id'; // if this is your PK
    public $incrementing = true;         // or false if not auto-increment
    protected $keyType = 'int';     

    protected $casts = [
        'token_svc_no' => 'integer',
    ];

    /**
     * Get the parent menu.
     */

    public function tokenlog()
    {
        return $this->belongsTo(TokenLog::class, 'svclogId');
    }


}
