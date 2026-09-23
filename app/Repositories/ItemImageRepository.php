<?php

namespace App\Repositories;

use App\Interfaces\ItemImageRepositoryInterface;
use App\Models\ItemAttribute;
use App\Models\ItemImage;
use Laravel\Sanctum\HasApiTokens;

class ItemImageRepository implements ItemImageRepositoryInterface
{
    use HasApiTokens;
    public function create($data)
    {
        return ItemImage::insert($data);
    }

    public function deleteByItemId($itemId){
        return ItemImage::where('item_id', $itemId)->delete();
    }
    public function deleteByUrl($itemId,$networkImageUrl)
    {
        return ItemImage::where('item_id',$itemId)->where('image_url','LIKE','%'.$networkImageUrl)->delete();
    }


}
