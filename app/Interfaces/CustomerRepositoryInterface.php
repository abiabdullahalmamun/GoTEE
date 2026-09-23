<?php

namespace App\Interfaces;



interface CustomerRepositoryInterface
{
    public function create($data);
    public function getById(int $id);
    public function getAll(bool $status);
    public function getLastCustomer(int $authId);
}
