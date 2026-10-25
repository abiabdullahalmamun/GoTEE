<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineEFF extends Model
{
    protected $table = 'tbl_line_eff';
    public $timestamps = false;
    protected $guarded = [];

    protected $fillable =[ 'line', 'floor', 'Date','OrderCode','totalTarget','operator','helper','totalMan','workHr','pcsQty','smv','veriance','tootalAvlHr','totalEarn','DayEff','perf','defect','acv','rDays'];

    protected $primaryKey = 'id'; // if this is your PK
    public $incrementing = true;         // or false if not auto-increment
    protected $keyType = 'int'; 

    public function lineData()
    {
        return $this->belongsTo(Line::class, 'line', 'id');
    }

    public function floorData()
    {
        return $this->belongsTo(Floor::class, 'floor', 'id');
    }

    public function orderData()
    {
        return $this->belongsTo(OrderNo::class, 'OrderCode', 'OrderCode');
    }
 
}
