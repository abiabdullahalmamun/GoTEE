<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SslAptList extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_apt_ssl';
    protected $fillable = [ 'WebFile_no', 'prev_date', 'curr_date', 'apt_hr','paystatus','checked_user','checked_on','txnId','amount','visatype','center','txn_date','checked_at','actVisa','apt_start','apt_end'];

    /**
     * Get the parent menu.
     */
    protected $primaryKey = 'id'; // if this is your PK
    public $incrementing = true;         // or false if not auto-increment
    protected $keyType = 'int'; 

    
    public function center()
    {
        return $this->belongsTo(Center::class, 'center');
    }
    public function visaType()
    {
        return $this->belongsTo(VisaTypeApt::class, 'visatype');
    }

}
