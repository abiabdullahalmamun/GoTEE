<?php

namespace App\Repositories;

use App\Interfaces\ItemRepositoryInterface;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class ItemRepository implements ItemRepositoryInterface
{
    use HasApiTokens;

    public function getAll(bool $status)
    {
        return Item::with(['defaultVariants','images','variants','attributes'])->orderBy('id','ASC')->get();
    }

    public function create($data) : ?Item
    {
        return Item::create($data);
    }

    public function getById(int $id) : ?Item
    {
        return Item::find($id);
    }
    public function getLastItem(int $authId)
    {
        return Item::orderby('id', 'desc')->first();
    }

    public function lastStockPerStore(int $itemId)
    {
       return Item::with('lastStockPerStore')->find($itemId);
    }

}
