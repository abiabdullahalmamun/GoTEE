<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'customer_id', 'payment_amount', 'payment_date', 'payment_method'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $order = Order::find($model->order_id);
            $totalPayments = $order->payments()->sum('payment_amount');
            if (($totalPayments + $model->payment_amount) > $order->total_amount) {
                throw new \Exception('Total payments exceed the order total amount');
            }
        });
    }
}
