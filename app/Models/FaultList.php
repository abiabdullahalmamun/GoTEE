<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaultList extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_defectName';
    protected $guarded = [];

    protected $fillable =[ 'defectCode', 'defectName', 'defectCatagory','defCat','status','created_by','defectCodeId' ];

    protected $primaryKey = 'sl'; // if this is your PK
    public $incrementing = true;         // or false if not auto-increment
    protected $keyType = 'int'; 

   public function defcat()
    {
        return $this->belongsTo(FaultCat::class, 'defCat');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
   
}
