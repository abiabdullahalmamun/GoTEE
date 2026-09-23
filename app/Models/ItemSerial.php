<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;

class ItemSerial extends Model
{
    use HasFactory;

    protected $fillable = ['store_id','item_id','value','value','is_stock','created_by', 'updated_by'];

    protected $hidden = [
        'created_by', 'updated_by','created_at','updated_at'
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
