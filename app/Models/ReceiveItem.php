<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReceiveItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'receive_id', 'issue_return_item_id','item_id', 'p_rate', 'rate', 'qty', 'amount','warranty_month', 'created_by','updated_by','created_at','updated_at'
    ];

    public function receive()
    {
        return $this->belongsTo(Receive::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class,'item_id','id');
    }

    public function stockSerials(): HasMany
    {
        return $this->hasMany(StockItemSerial::class, 'receive_item_id', 'id');
    }
}
