<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BundleInfo extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_bundleInfo';

    protected $fillable =[ 'Date', 'bundle_no', 'batchNo','CutNo','bundle_rfid','PrintedSerial','created_at','bundle_qty','orderRef','OrderCode','buyer','style_no','color','size','status','in_production','DeviceID','operator','created_at','updated_at','lineID','lineIn_by','lineIn_at','RollNo','Shade','Item','sl_start','sl_stop','po_no','trimsId' ];

    protected $primaryKey = 'id'; // if this is your PK
    public $incrementing = true;         // or false if not auto-increment
    protected $keyType = 'int'; 


    public function line()
    {
        return $this->belongsTo(Device::class, 'lineID' );
    }
    public function device()
    {
        return $this->belongsTo(Device::class, 'DeviceID', 'devID');
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'operatorId');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'operator');
    }
    public function userLine()
    {
        return $this->belongsTo(User::class, 'lineIn_by');
    }

}
