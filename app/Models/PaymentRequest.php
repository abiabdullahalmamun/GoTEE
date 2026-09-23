<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class PaymentRequest extends Model
{
    use Loggable;
    protected $table = 'payment_requests';
    public $timestamps = false;

    protected $fillable = [
        'data',
        'txn_no',
        'status',
        'created_by',
        'created_at'
    ];
}
