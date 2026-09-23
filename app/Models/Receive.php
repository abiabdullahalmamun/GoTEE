<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receive extends Model
{
    use HasFactory;

    protected $fillable = [
            'issue_return_id',
            'trans_date',
            'trans_no',
            'grn_no',
            'grn_date',
            'trans_type_id',
            'store_id',
            'supplier_id',
            'sub_total_amount',
            'total_amount',
            'paid_amount',
            'due_amount',
            'remark',
            'created_by',
            'created_at',
            'updated_by',
            'updated_at',
    ];


    public function supplier(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id','id');
    }

    public function store(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id','id');
    }

    public function transType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TransType::class, 'trans_type_id','id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by','id');
    }


    public function receiveItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ReceiveItem::class,'receive_id','id');
    }


    public function scopeTotalPayments($query)
    {
        return $query->withSum('payments as total_payments', 'payment_amount');
    }
}
