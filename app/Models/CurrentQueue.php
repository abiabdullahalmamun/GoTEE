<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrentQueue extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_q_queue';
    protected $fillable = ['centerId', 'Date', 'token_type', 'token_svc_no','token_number','cnt'];
}
