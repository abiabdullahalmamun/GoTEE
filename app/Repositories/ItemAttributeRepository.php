<?php

namespace App\Repositories;

use App\Interfaces\ItemAttributeRepositoryInterface;
use App\Models\ItemAttribute;
use App\Models\ItemVariant;
use Laravel\Sanctum\HasApiTokens;

class ItemAttributeRepository implements ItemAttributeRepositoryInterface
{
    use HasApiTokens;
    public function create($data)
    {
      return ItemAttribute::insert($data);
    }
    public function deleteByItemId($itemId)
    {
      return ItemAttribute::where('item_id',$itemId)->delete();
    }
}
