<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataReplication extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'replication_status';
    protected $fillable = ['server_name','master_host','io_running','sql_running','seconds_behind_master', 'last_error',  'status', 'last_checked','created_at','updated_at'];

    protected $primaryKey = 'id'; // if this is your PK
    public $incrementing = true;         // or false if not auto-increment
    protected $keyType = 'int';     

    /**
     * Get the parent menu.
     */
}
