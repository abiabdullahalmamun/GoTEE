<?php

namespace  App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationCat extends Model
{
    protected $table = 'tbl_operationCategory';
    protected $primaryKey = 'sl';

    // If sl is auto-incrementing:
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [ 'Category_id','Category', 'status',  'created_by', 'created_at', 'updated_at' ];
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
