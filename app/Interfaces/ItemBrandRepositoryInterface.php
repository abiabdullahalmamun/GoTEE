<?php

namespace App\Interfaces;

interface ItemBrandRepositoryInterface
{
    public function create($data);
    public function getById(int $id);
    public function getAll(bool $status);
}
