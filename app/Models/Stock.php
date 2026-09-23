<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;

class Stock extends Model
{
    use HasFactory;
    protected $fillable = [
        'stock_date',
        'stock_no',
        'trans_type_id',
        'receive_id',
        'issue_id',
        'customer_id',
        'supplier_id',
        'total_amount',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
