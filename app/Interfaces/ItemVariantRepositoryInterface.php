<?php

namespace App\Interfaces;


interface ItemVariantRepositoryInterface
{
    public function create($data);
    public function createMany($data);


}
