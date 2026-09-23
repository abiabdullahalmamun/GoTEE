<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueueCode extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_q_code';
    protected $fillable = [ 'Date', 'centerId', 'token', 'svc','web','random','status','mac', 'svclogId','txnId','scantime'];

    protected $primaryKey = 'id'; // if this is your PK
    public $incrementing = true;         // or false if not auto-increment
    protected $keyType = 'int'; 
    /**
     * Get the parent menu.
     */

    public function center()
    {
        return $this->belongsTo(Center::class, 'center');
    }
    public function service()
    {
        return $this->belongsTo(Service::class, 'svc');
    }
    public function tokenlog()
    {
        return $this->belongsTo(TokenLog::class, 'svclogId');
    }

}
