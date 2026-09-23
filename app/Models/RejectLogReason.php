<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RejectLogReason extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_reject_log_cause';
    protected $fillable = ['web_ref', 'reasonId','created_by'];

    /**
     * Get the parent menu.
     */

    public function webref()
    {
        return $this->belongsTo(RejectLog::class, 'web_ref');
    }
    public function rejectcause()
    {
        return $this->belongsTo(RejectReason::class, 'reasonId');
    }

}
