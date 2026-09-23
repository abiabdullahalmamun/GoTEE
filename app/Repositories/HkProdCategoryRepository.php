<?php

namespace App\Repositories;

use App\Interfaces\HkProdCategoryRepositoryInterface;
use App\Models\HkProdCategory;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class HkProdCategoryRepository implements HkProdCategoryRepositoryInterface
{
    use HasApiTokens;


    public function create($data) : ?HkProdCategory
    {
        return HkProdCategory::create($data);
    }

    public function getById(int $id) : ?HkProdCategory
    {
        return HkProdCategory::find($id);
    }
    public function getAllHk($status = true)
    {
        return HkProdCategory::where('is_active', $status)->orderBy('id','ASC')->get();
    }

    public function getAll(array $filters = [], int $perPage = 10)
    {
        $query = HkProdCategory::query();

        // Filter by status
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        // Search
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('slug', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('code', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'id';
        $sortDir = $filters['sort_dir'] ?? 'desc';
        $query->orderBy($sortBy, $sortDir);

        return $query->paginate($perPage);
    }

}
