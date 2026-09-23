<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UndelPass extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_undel_pass';

    protected $fillable =['centerId','Date','passport','app_ref', 'status','created_by'];

    protected $primaryKey = 'id'; // if this is your PK
    public $incrementing = true;         // or false if not auto-increment
    protected $keyType = 'int'; 


    public function center()
    {
        return $this->belongsTo(Center::class, 'centerId');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function app_ref()
    {
        return $this->belongsTo(AppReceive::class, 'app_ref');
    }

}
