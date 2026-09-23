<?php

namespace App\Interfaces;

interface UomRepositoryInterface
{
    public function create($data);
    public function getById(int $id);
    public function getAll(bool $status);
    public function getLastOne();
}
