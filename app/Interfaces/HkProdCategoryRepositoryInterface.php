<?php

namespace App\Interfaces;

interface HkProdCategoryRepositoryInterface
{
    public function create($data);
    public function getById(int $id);
    public function getAllHk(bool $status);
    public function getAll(array $filters, int $perPage);
}
