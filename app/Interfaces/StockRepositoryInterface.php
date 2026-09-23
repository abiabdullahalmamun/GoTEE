<?php

namespace App\Interfaces;

use App\Models\Shift;


interface StockRepositoryInterface
{
    public function create($data);
    public function getById(int $id);
    public function getAll(int $storeId,int | null $status);
    public function getLastStock();
}
