<?php

namespace  App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessGrp extends Model
{
    protected $table = 'tbl_process_grp';
    protected $fillable = [ 'company_id','groupName', 'status',  'created_by', 'created_at', 'updated_at'];
    protected $guarded = [];
}
