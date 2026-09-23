<?php

namespace App\Repositories;

use App\Interfaces\ItemVariantRepositoryInterface;
use App\Models\ItemVariant;
use Laravel\Sanctum\HasApiTokens;

class ItemVariantRepository implements ItemVariantRepositoryInterface
{

    use HasApiTokens;
    public function create($data)
    {
      return ItemVariant::create($data);
    }
    public function deleteByItemId($itemId)
    {
      return ItemVariant::where('item_id',$itemId)->delete();
    }
    public function createMany($data)
    {
      return ItemVariant::insert($data);
    }


}
