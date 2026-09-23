<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SewingMachine extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_dev_sewing_mc';

    protected $fillable =[ 'mcId', 'start', 'stop','scanInt','ref','created_by','po_no','mctype','description','brand','model','mfg_no','AssetNo' ];

    protected $primaryKey = 'id'; // if this is your PK
    public $incrementing = true;         // or false if not auto-increment
    protected $keyType = 'int'; 


    public function sewingmc()
    {
        return $this->belongsTo(SewingMachine::class, 'sewingmcId', 'mcId');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
   

}
