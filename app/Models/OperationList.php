<?php

namespace  App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationList extends Model
{
    protected $table = 'tbl_operationList';
    protected $primaryKey = 'sl';

    // If sl is auto-incrementing:
    public $incrementing = true;
    protected $keyType = 'int';
    
    protected $fillable = [ 'OrderCode','OprNo', 'OperationName',  'OperationCode', 'MC_Type', 'SMV','Target','ManPower' , 'Balance','Operation_Target','MC_Target','OperationSeqNo','IsLastOperation','status','created_by','grpCode' ];

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function order()
    {
        return $this->belongsTo(OrderNo::class, 'OrderCode','OrderCode');
    }
    public function mctype()
    {
        return $this->belongsTo(MachineType::class, 'MC_Type');
    }
    public function operation()
    {
        return $this->belongsTo(OpnCodeBank::class, 'OperationCode','OprNo');
    }
    public function group()
    {
        return $this->belongsTo(ProcessGrp::class, 'grpCode');
    }
}
