<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'item_id', 'variant_id', 'quantity', 'item_rate', 'item_variant_extra_amount', 'total_amount'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class,'item_id','id');
    }

    public function variant()
    {
        return $this->belongsTo(ItemVariant::class);
    }
}
