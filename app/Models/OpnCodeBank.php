<?php

namespace  App\Models;

use Illuminate\Database\Eloquent\Model;

class OpnCodeBank extends Model
{
    protected $table = 'tbl_operationCode';
    protected $primaryKey = 'sl';

    // If sl is auto-incrementing:
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [ 'company_id','OprNo', 'Category',  'OperationName', 'status', 'created_by','created_at','updated_at','def_opn'];
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cat()
    {
        return $this->belongsTo(OperationCat::class, 'Category','Category_id');
    }
}
