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

    protected $fillable =['companyId', 'devID', 'devType', 'ip','mac','status','created_by','location','ssid','passkey','message','opstate'];

    protected $primaryKey = 'id'; // if this is your PK
    public $incrementing = true;         // or false if not auto-increment
    protected $keyType = 'int'; 


    public function center()
    {
        return $this->belongsTo(Center::class, 'companyId');
    }

    // public function region()
    // {
    //     return $this->belongsTo(Region::class, 'regionId');
    // }
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
   

}
