<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppDigitization extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_digitization';
    protected $fillable = ['Date', 'regionId', 'webfile', 'status', 'remarks','created_by','deposite_date','return_date','dvd_date','webref','sent2hci_date'];

    protected $primaryKey = 'id'; // if this is your PK
    public $incrementing = true;         // or false if not auto-increment
    protected $keyType = 'int';     

    /**
     * Get the parent menu.
     */

    public function region()
    {
        return $this->belongsTo(Region::class, 'regionId');
    }
 
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function webReference()
    {
        return $this->belongsTo(AppReceive::class,'webref');
    }

}
