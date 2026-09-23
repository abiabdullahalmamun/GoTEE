<?php

namespace App\Repositories;

use App\Interfaces\SupplierRepositoryInterface;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class SupplierRepository implements SupplierRepositoryInterface
{
    use HasApiTokens;

    public function getAll(bool $status)
    {
        return Supplier::get();
    }

    public function create($data) : ?Supplier
    {
        return Supplier::create($data);
    }

    public function getById(int $id) : ?Supplier
    {
        return Supplier::find($id);
    }
    public function getLastSupplier(int $authId)
    {
        return Supplier::orderby('id', 'desc')->first();
    }

}
