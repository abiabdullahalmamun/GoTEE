<?php

namespace App\Interfaces;

use App\Models\Shift;


interface ShiftRepositoryInterface
{
    public function create($data);
    public function getById(int $id);
    public function getAll(bool $status);
}
