<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IssueItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_id',
        'receive_return_item_id',
        'item_id',
        'p_rate',
        'rate',
        'qty',
        'amount',
        'discount',
        'total',
        'warranty_month',
        'created_by','updated_by','created_at','updated_at'
    ];

    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class,'item_id','id');
    }

    public function stockSerials(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StockItemSerial::class,'issue_item_id','id');
    }


    public function IssueReturnItemSerial(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(
            StockItemSerial::class,
            ReceiveItem::class,
            'issue_return_item_id',
            'receive_item_id',
            'id',
            'id'
        );
    }

    public function issueReturnItems(): HasMany
    {
        return $this->hasMany(IssueReturnItem::class, 'issue_item_id', 'id');
    }
//
//    public function issueItemReturnQty(): \Illuminate\Database\Eloquent\Relations\HasMany
//    {
//        return $this->hasMany(IssueReturnItem::class,'issue_item_id','id');
//    }

}
