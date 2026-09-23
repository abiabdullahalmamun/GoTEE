<?php

namespace App\Repositories;

use App\Interfaces\ItemBrandRepositoryInterface;
use App\Models\ItemBrand;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class ItemBrandRepository implements ItemBrandRepositoryInterface
{
    use HasApiTokens;


    public function create($data) : ?ItemBrand
    {
        return ItemBrand::create($data);
    }

    public function getById(int $id) : ?ItemBrand
    {
        return ItemBrand::find($id);
    }

    public function getAll(bool $status)
    {
        return ItemBrand::where('is_active',$status)->get();
    }
}
