<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineQC extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_line_qc_dev';

    protected $fillable =[ 'lineId', 'appId', 'devId','created_by','created_at','updated_at','status'];

    protected $primaryKey = 'id'; // if this is your PK
    public $incrementing = true;         // or false if not auto-increment
    protected $keyType = 'int'; 


    // public function center()
    // {
    //     return $this->belongsTo(Center::class, 'companyId');
    // }
    public function device()
    {
        return $this->belongsTo(Device::class, 'devId', 'devID');
    }
    public function line()
    {
        return $this->belongsTo(Line::class, 'lineId');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
   

}
