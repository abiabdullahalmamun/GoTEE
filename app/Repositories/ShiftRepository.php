<?php

namespace App\Repositories;

use App\Interfaces\ShiftRepositoryInterface;
use App\Models\HkProdCategory;
use App\Models\Shift;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class ShiftRepository implements ShiftRepositoryInterface
{
    use HasApiTokens;

    public function getAll(bool $status)
    {
        return Shift::where('user_id', Auth::id())->where('is_active',$status)->get();
    }

    public function create($data) : ?Shift
    {
        return Shift::create($data);
    }

    public function getById(int $id) : ?Shift
    {
        return Shift::find($id);
    }

}
