<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_floor';

    protected $fillable =[ 'floor_id', 'status', 'created_by','created_at','updated_at','factoryId'];

    protected $primaryKey = 'id'; // if this is your PK
    public $incrementing = true;         // or false if not auto-increment
    protected $keyType = 'int'; 


    // public function center()
    // {
    //     return $this->belongsTo(Center::class, 'companyId');
    // }

    // public function sewingmc()
    // {
    //     return $this->belongsTo(SewingMachine::class, 'sewingmcId');
    // }
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
   

}
