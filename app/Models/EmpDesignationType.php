<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpDesignationType extends Model
{
    protected $table = 'tbl_emp_des_type';
    protected $primaryKey = 'id'; // if this is your PK
    public $incrementing = true;         // or false if not auto-increment
    protected $keyType = 'int';

    protected $guarded = [];
    protected $fillable = ['typename', 'status', 'created_by', 'created_at','updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
