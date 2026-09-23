<?php

namespace App\Repositories;

use App\Interfaces\StoreRepositoryInterface;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class StoreRepository implements StoreRepositoryInterface
{
    use HasApiTokens;


    public function create($data) : ?Store
    {
        return Store::create($data);
    }

    public function getById(int $id) : ?Store
    {
        return Store::find($id);
    }

    public function getAll(bool $status)
    {
        return Store::orderBy('id','ASC')->get();
    }
}
