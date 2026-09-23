<?php

namespace App\Repositories;

use App\Interfaces\ItemBrandRepositoryInterface;
use App\Interfaces\ItemSerialRepositoryInterface;
use App\Models\ItemBrand;
use App\Models\ItemSerial;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class ItemSerialRepository implements ItemSerialRepositoryInterface
{
    use HasApiTokens;


    public function create($data) : ?ItemSerial
    {
        return ItemSerial::create($data);
    }

    public function getById(int $id) : ?ItemSerial
    {
        return ItemSerial::find($id);
    }
    public function getBySerialNo(string $serialNo) : ?ItemSerial
    {
        return ItemSerial::where('value', $serialNo)->latest()->first();
    }
    public function getByItemSerialNo(string $serialNo,int $itemId) : ?ItemSerial
    {
        return ItemSerial::where('value', $serialNo)->where('item_id',$itemId)->latest()->first();
    }

    public function getAll(bool $status)
    {
        return ItemSerial::where('is_stock',true)->get();
    }
}
