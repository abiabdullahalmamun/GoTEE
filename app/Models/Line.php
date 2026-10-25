<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Line extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_line';

    protected $fillable =[ 'lineID', 'lineType', 'line_name','floorID','created_by','status','created_at','updated_at','OrderCode','rfid','com_code'];

    protected $primaryKey = 'id'; // if this is your PK
    public $incrementing = true;         // or false if not auto-increment
    protected $keyType = 'int'; 


    // public function center()
    // {
    //     return $this->belongsTo(Center::class, 'companyId');
    // }

    public function floor()
    {
        return $this->belongsTo(Floor::class, 'floorID');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
   

}
