<?php

namespace App\Interfaces;

use App\Models\Shift;


interface ItemRepositoryInterface
{
    public function create($data);
    public function getById(int $id);
    public function getAll(bool $status);
    public function getLastItem(int $authId);
    public function lastStockPerStore(int $itemId);
}
