<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_service';
    protected $fillable = ['center_id', 'service_name', 'defultsec', 'status','created_by','type'];

    protected $primaryKey = 'id'; // if this is your PK
    public $incrementing = true;         // or false if not auto-increment
    protected $keyType = 'int'; 

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
