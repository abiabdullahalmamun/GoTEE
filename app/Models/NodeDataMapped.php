<?php


namespace  App\Models;

use Illuminate\Database\Eloquent\Model;

class NodeDataMapped extends Model
{
    protected $table = 'tbl_nodedata_mapped';
    protected $guarded = [];
    protected $fillable = ['company_id', 'Date', 'hr', 'eventTime','tagID','PrintedSerial','OrderCode','orderName','bundleSl','BundleNo','CutNo','color','BundleQty','terminal','workstation','operatorID','starttime','stoptime','totalSec','lineNo','floor','processID','oprNo','created_at','updated_at','status','Remarks'];

    protected $primaryKey = 'sl'; // if this is your PK
    public $incrementing = true;         // or false if not auto-increment
    protected $keyType = 'int';


    public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

    // public function order()
    // {
    //     return $this->belongsTo(OrderNo::class, 'OrderCode', 'OrderCode');
    // }

}
