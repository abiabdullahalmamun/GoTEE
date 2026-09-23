<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RejectLog extends Model
{
    use Loggable, HasFactory;

    protected $table = 'tbl_reject_log';

    protected $fillable = [
        'Date', 'centerId', 'Webfile',
        'ApplicantName','passport','contact',
        'visatype','created_by'
    ];

    public function center()
    {
        return $this->belongsTo(Center::class, 'centerId');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function visa()
    {
        return $this->belongsTo(VisaType::class, 'visatype');
    }

    //  ONE reject log → MANY reasons (pivot table)
    public function rejectLogReasons()
    {
        return $this->hasMany(RejectLogReason::class, 'web_ref', 'id');
    }

    // Direct access to reason names
    public function rejectReasons()
    {
        return $this->hasManyThrough(
            RejectReason::class,
            RejectLogReason::class,
            'web_ref',     // FK on tbl_reject_log_cause
            'id',          // PK on reject_reasons
            'id',          // PK on tbl_reject_log
            'reasonId'     // FK on tbl_reject_log_cause
        );
    }
}
