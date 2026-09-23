<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotorStatus extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_motor_status';

    protected $fillable =[ 'Date', 'devId', 'mt_start','mt_stop','mt_run','ac_rms','created_at','updated_at','operatorId','status','bundleId' ];

    protected $primaryKey = 'id'; // if this is your PK
    public $incrementing = true;         // or false if not auto-increment
    protected $keyType = 'int'; 


    public function device()
    {
        return $this->belongsTo(Device::class, 'devId', 'devID');
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'operatorId');
    }
   

}
