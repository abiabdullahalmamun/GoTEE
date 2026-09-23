<?php

namespace App\Interfaces;

use App\Models\Shift;


interface ReceiveRepositoryInterface
{
    public function create($data);
    public function getById(int $id);
    public function getAllReceive(int $storeId,int $transTypeId);
    public function getLastReceive();
}
