<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppLog extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_application_log';
    protected $fillable = ['Date', 'regionId', 'centerId', 'web_ref', 'stepId', 'remarks','created_by'];

    protected $primaryKey = 'id'; // if this is your PK
    public $incrementing = true;         // or false if not auto-increment
    protected $keyType = 'int';     

    /**
     * Get the parent menu.
     */

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
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
    public function appsteps()
    {
        return $this->belongsTo(AppSteps::class, 'stepId');
    }

}
