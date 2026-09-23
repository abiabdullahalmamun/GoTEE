<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_date', 'invoice_no', 'customer_id', 'supplier_id', 'sub_total',
        'total_discount', 'total_payable','order_status_code','is_paid','trans_resp'
    ];

    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id','id');
    }

    public function supplier(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'supplier_id','id');
    }

    public function orderItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItem::class,'order_id','id');
    }

    public function orderStatus(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(OrderStatus::class,'order_status_code','code');
    }


    public function payments()
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function scopeTotalPayments($query)
    {
        return $query->withSum('payments as total_payments', 'payment_amount');
    }
}
