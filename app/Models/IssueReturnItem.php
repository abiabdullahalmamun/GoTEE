<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class IssueReturnItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_return_id',
        'issue_item_id',
        'item_id',
        'p_rate',
        'rate',
        'qty',
        'amount',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function issueReturn()
    {
        return $this->belongsTo(IssueReturn::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class,'item_id','id');
    }

    public function stockSerials()
    {
        return $this->hasManyThrough(StockItemSerial::class, ReceiveItem::class, 'issue_return_item_id', 'receive_item_id');
    }

    public function receiveItem(): HasOne
    {
        return $this->hasOne(ReceiveItem::class, 'issue_return_item_id', 'id');
    }

}
