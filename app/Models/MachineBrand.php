<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineBrand extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_machine_brand';

    protected $fillable =[ 'brandname', 'status', 'created_by','created_at','updated_at' ];

    protected $primaryKey = 'id'; // if this is your PK
    public $incrementing = true;         // or false if not auto-increment
    protected $keyType = 'int'; 


    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
