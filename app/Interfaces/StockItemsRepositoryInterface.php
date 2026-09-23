<?php

namespace App\Interfaces;

use App\Models\Shift;


interface StockItemsRepositoryInterface
{
    public function create($data);
    public function getById(int $id);
    public function getAll(bool $status);
    public function checkItemStockStatusByStore(int $itemId,int $storeId);
}
