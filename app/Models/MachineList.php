<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineList extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_machine';

    protected $fillable =[ 'company_id', 'machine_id', 'machine_brand','machine_type','created_by','machine_model','Description','rented','rented_ref','status','created_at','updated_at' ];

    protected $primaryKey = 'id'; // if this is your PK
    public $incrementing = true;         // or false if not auto-increment
    protected $keyType = 'int'; 


    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function brand()
    {
        return $this->belongsTo(MachineBrand::class, 'machine_brand');
    }
    public function type()
    {
        return $this->belongsTo(MachineType::class, 'machine_type');
    }


}
