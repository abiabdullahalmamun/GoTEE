<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;

class OrderStatus extends Model
{
    use HasFactory;
    protected $table = 'order_status';

    protected $fillable = [];
//    protected $fillable = ['name', 'code', 'is_active'];

    public function orders()
    {
        return $this->hasMany(Order::class, 'order_status_code', 'code');
    }
}
