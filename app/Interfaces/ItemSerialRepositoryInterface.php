<?php

namespace App\Interfaces;

interface ItemSerialRepositoryInterface
{
    public function create($data);
    public function getById(int $id);
    public function getBySerialNo(string $serialNo);
    public function getByItemSerialNo(string $serialNo,int $itemId);
    public function getAll(bool $status);
}
