<?php

namespace App\Interfaces;

interface StoreRepositoryInterface
{
    public function create($data);
    public function getById(int $id);
    public function getAll(bool $status);
}
