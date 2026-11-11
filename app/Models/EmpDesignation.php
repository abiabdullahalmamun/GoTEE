<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpDesignation extends Model
{
    protected $table = 'tbl_emp_designation';
    protected $primaryKey = 'id'; // if this is your PK
    public $incrementing = true;         // or false if not auto-increment
    protected $keyType = 'int';

    protected $guarded = [];
    protected $fillable = ['company_id', 'designation', 'created_by', 'created_at','updated_at','groupId','status'];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function destype()
    {
        return $this->belongsTo(EmpDesignationType::class, 'groupId');
    }
}
