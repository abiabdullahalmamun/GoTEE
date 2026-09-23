<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_counter';
    protected $fillable = ['region_id', 'center_id', 'counter_id', 'counter_name','created_by','status','mac','ip','host','token','loginstate','loginId','tokenno','autoMan','visaType','stickerType','enCall'];

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
        return $this->belongsTo(Center::class, 'center_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function loginid()
    {
        return $this->belongsTo(User::class, 'loginId');
    }
    public function visa()
    {
        return $this->belongsTo(VisaType::class, 'visaType');
    }
    public function sticker()
    {
        return $this->belongsTo(StickerMap::class, 'stickerType');
    }
    public function counterServices()
    {
        return $this->hasMany(CounterSvc::class, 'counterId', 'id');
    }
}
