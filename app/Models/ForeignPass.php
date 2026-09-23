<?php

namespace App\Models;
use App\Models\MoneyReceipt;
use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForeignPass extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_application_foreign';
    protected $fillable = ['Date','centerId', 'web_ref', 'gratis', 'BookNo','ReceiptNo','nationality','created_by','remarks','visa_fee','fax_trans_charge','icwf','visa_app_charge','total_amount','rupee_rate','duration','entryType','total_rupee'];

    protected $primaryKey = 'id'; // if this is your PK
    public $incrementing = true;         // or false if not auto-increment
    protected $keyType = 'int';     

    /**
     * Get the parent menu.
     */
 
    public function entry()
    {
        return $this->belongsTo(EntryType::class, 'entryType');
    }
    public function v_duration()
    {
        return $this->belongsTo(VisaDuration::class, 'duration');
    }
    public function center()
    {
        return $this->belongsTo(Center::class, 'centerId');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function webref()
    {
        return $this->belongsTo(AppReceive::class, 'web_ref');
    }
 
 
}
