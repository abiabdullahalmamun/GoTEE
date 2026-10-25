<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QCfault extends Model
{
    // use Loggable;
    use HasFactory;
    protected $table = 'tbl_QC_faultLog';
    protected $guarded = [];
    protected $fillable =[ 'company_id', 'Date', 'hr','OrderCode','batchNo','CutNo','color','size','bundleNo','RFID_serial','OperationCode','operatorID','DefectCode','devID','qcId','qcType','created_at','updated_at','Qty','line','logincode','bundleSl' ];

    protected $primaryKey = 'sl'; // if this is your PK
    public $incrementing = true;         // or false if not auto-increment
    protected $keyType = 'int'; 

    public function emp()
    {
        return $this->belongsTo(Employee::class, 'qcId');
    }
    public function oprt()
    {
        return $this->belongsTo(Employee::class, 'operatorID');
    }
    public function bundle()
    {
        return $this->belongsTo(BundleInfo::class, 'bundleSl');
    }
}
