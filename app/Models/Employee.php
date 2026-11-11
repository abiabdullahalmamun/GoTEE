<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_employee';
    protected $primaryKey = 'id'; // if this is your PK
    public $incrementing = true;         // or false if not auto-increment
    protected $keyType = 'int';

    protected $fillable = ['company_id', 'emp_id', 'emp_name', 'emp_desig','Gender','gender1','Date_of_join','evaluation','remarks','present_grade','present_salary','card_id','pin','status','emp_type','created_by','created_at','updated_at','gender1'];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function desig()
    {
        return $this->belongsTo(EmpDesignation::class, 'emp_desig');
    }
    
    public function grade()
    {
        return $this->belongsTo(EmpGrade::class, 'present_grade');
    }
}
