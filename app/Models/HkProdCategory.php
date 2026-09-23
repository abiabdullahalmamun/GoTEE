<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;

class HkProdCategory extends Model
{
    use HasFactory;

    protected $fillable = ['code','slug','name', 'sequence', 'is_active','created_by', 'updated_by'];

    protected $hidden = [
        'created_by', 'updated_by','created_at','updated_at'
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
