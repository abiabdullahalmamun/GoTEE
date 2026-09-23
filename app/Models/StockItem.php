<?php

namespace App\Models;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id',
        'stock_id',
        'receive_item_id',
        'issue_item_id',
        'item_id',
        'p_rate',
        'op_qty',
        'op_rate',
        'op_amount',
        'trans_qty',
        'trans_rate',
        'trans_amount',
        'cls_qty',
        'cls_rate',
        'cls_amount',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public static function getLastStock($itemId,$storeId)
    {
        return self::where('item_id', $itemId)
            ->where('store_id', $storeId)
            ->latest('id')
            ->first();
    }

    public static function getLastStockQty($itemId,$storeId)
    {
        return self::where('item_id', $itemId)
            ->where('store_id', $storeId)
            ->latest('id')
            ->value('cls_qty');
    }

}
