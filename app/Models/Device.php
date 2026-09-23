<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_dev_masterInfo';

    protected $fillable =[ 'devID', 'devType', 'ip','mac','status','created_by','sewingmcId','lineID','floorID','lastCom','ActiveOpn','ssid','passkey','message','opstate','firmwareUp','version','motor_state','fwstate','pcSec'];

    protected $primaryKey = 'id'; // if this is your PK
    public $incrementing = true;         // or false if not auto-increment
    protected $keyType = 'int'; 


    // public function center()
    // {
    //     return $this->belongsTo(Center::class, 'companyId');
    // }

    public function sewingmc()
    {
        return $this->belongsTo(SewingMachine::class, 'sewingmcId');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
   

}
