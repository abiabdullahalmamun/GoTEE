<?php

namespace App\Interfaces;



interface SupplierRepositoryInterface
{
    public function create($data);
    public function getById(int $id);
    public function getAll(bool $status);
    public function getLastSupplier(int $authId);
}
