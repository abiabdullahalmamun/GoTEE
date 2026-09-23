<?php

namespace App\Repositories;

use App\Interfaces\StockItemsRepositoryInterface;
use App\Interfaces\ReceiveRepositoryInterface;
use App\Models\Order;
use App\Models\StockItem;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class StockItemRepository implements StockItemsRepositoryInterface
{
    use HasApiTokens;

    public function getAll(bool $status)
    {
        return StockItem::where('is_active',$status)->get();
    }

    public function create($data): StockItem
    {
        return StockItem::create($data);
    }

    public function getById(int $id) : ?StockItem
    {
        return StockItem::find($id);
    }

    public function checkItemStockStatusByStore($itemId,$storeId) : ? bool
    {
        return StockItem::where('item_id',$itemId)->where('store_id',$storeId)->exists();
    }



}
