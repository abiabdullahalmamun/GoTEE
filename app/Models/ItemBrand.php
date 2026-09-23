<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;

class ItemBrand extends Model
{
    use HasFactory;

    protected $fillable = ['name','sequence','is_active','created_by', 'updated_by'];

    protected $hidden = [
        'created_by', 'updated_by','created_at','updated_at','is_active'
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
