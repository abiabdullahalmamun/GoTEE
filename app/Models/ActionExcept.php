<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionExcept extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'audit_except';
    protected $fillable = ['Date','module','centerId','userId','action', 'remarks',  'created_at', 'updated_at','ip_address'];

    protected $primaryKey = 'id'; // if this is your PK
    public $incrementing = true;         // or false if not auto-increment
    protected $keyType = 'int';     

    /**
     * Get the parent menu.
     */

    public function center()
    {
        return $this->belongsTo(Center::class, 'centerId');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
}
