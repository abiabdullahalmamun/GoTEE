<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceSvc extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_display_svc';

    protected $fillable =['devId','centerId', 'svcId'];

    protected $primaryKey = 'id'; // if this is your PK
    public $incrementing = true;         // or false if not auto-increment
    protected $keyType = 'int'; 


    public function center()
    {
        return $this->belongsTo(Center::class, 'centerId');
    }

    public function device()
    {
        return $this->belongsTo(Device::class, 'devId');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'svcId');
    }


}
