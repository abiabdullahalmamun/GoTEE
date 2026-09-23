<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppReceive extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_applicantInfo';
    protected $fillable = ['regionId', 'centerId', 'Date', 'Webfile', 'ApplicantName','passport','stickertype','stickerNo','status','contact','visatype','family_id','pmethod','txn','remarks','psQty','bioType','corrFee','created_by','stepId','cntNo','tknNo','profee','spfee','tdd','codeId','payId'];


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
    public function center()
    {
        return $this->belongsTo(Center::class, 'centerId');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function visa()
    {
        return $this->belongsTo(VisaType::class, 'visatype');
    }
    public function sticker()
    {
        return $this->belongsTo(StickerMap::class, 'stickertype');
    }
    public function appsteps()
    {
        return $this->belongsTo(AppSteps::class, 'stepId');
    }
    public function codes()
    {
        return $this->belongsTo(QueueCode::class, 'codeId');
    }
    public function pays()
    {
        return $this->belongsTo(SslAptList::class, 'payId');
    }
}
