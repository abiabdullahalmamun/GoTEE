<?php

namespace App\Interfaces;

use App\Models\Shift;


interface ReceiveItemsRepositoryInterface
{
    public function create($data);
    public function getById(int $id);
    public function getAll(bool $status);

    public function checkItemReceiveStatus(int $itemId);
}
