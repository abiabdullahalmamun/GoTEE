<?php

namespace App\Repositories;

use App\Interfaces\UomRepositoryInterface;
use App\Models\Uom;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class UomRepository implements UomRepositoryInterface
{
    use HasApiTokens;


    public function create($data) : ?Uom
    {
        return Uom::create($data);
    }

    public function getById(int $id) : ?Uom
    {
        return Uom::find($id);
    }

    public function getAll(bool $status)
    {
        return Uom::where('is_active',$status)->get();
    }

    public function getLastOne()
    {
        return Uom::orderBy('id','DESC')->first();
    }
}
