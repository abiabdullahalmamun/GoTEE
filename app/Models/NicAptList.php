<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NicAptList extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_apt_nic';
    protected $fillable = ['regionId', 'webfile', 'passport', 'Name', 'contact','reg_date','created_by'];

    /**
     * Get the parent menu.
     */

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }


}
