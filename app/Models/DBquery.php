<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DBquery extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_forQueryBuilder';
    protected $fillable = ['table_name','status'];

    protected $primaryKey = 'id'; // if this is your PK
    public $incrementing = true;         // or false if not auto-increment
    protected $keyType = 'int';     

 
    /**
     * Get the parent menu.
     */
}
