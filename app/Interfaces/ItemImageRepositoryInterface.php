<?php

namespace App\Interfaces;


interface ItemImageRepositoryInterface
{
    public function create($data);
    public function deleteByItemId($itemId);
    public function deleteByUrl($itemId, $networkImageUrl);

}
