<?php

namespace App\Interfaces;

use App\Models\Shift;


interface OrderRepositoryInterface
{
    public function create($data);
    public function getById(int $id);
    public function getAll(int $authId,int | null $status);
    public function getLastOrder();
}
