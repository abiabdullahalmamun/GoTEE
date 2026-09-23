<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemVariant extends Model
{
    use HasFactory;

    protected $fillable = ['item_id', 'name', 'value', 'regular_price','sell_price','init_stock_qty','created_by','updated_by','created_at','updated_at'];

   
}
