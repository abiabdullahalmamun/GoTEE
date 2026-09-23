<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortReceive extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_port_update';
    protected $fillable = ['applicant_name', 'passport', 'centerId', 'visa_no', 'visa_type','contact','Date','status','regionId','Remarks','Fee','OldPort','NewPort','stickerNo','tdd','created_by','created_at','updated_at','stepId'];


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
        return $this->belongsTo(VisaType::class, 'visa_type');
    }
    // public function sticker()
    // {
    //     return $this->belongsTo(StickerMap::class, 'stickertype');
    // }
    public function appsteps()
    {
        return $this->belongsTo(AppSteps::class, 'stepId');
    }

}