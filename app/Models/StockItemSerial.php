<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockItemSerial extends Model
{
    use HasFactory;

    protected $fillable = [
        'receive_item_id',
        'issue_item_id',
        'trans_type_id',
        'item_id',
        'item_serial_id',
    ];

    public function receiveItem()
    {
        return $this->belongsTo(ReceiveItem::class, 'receive_item_id','id');
    }

    public function issueItem()
    {
        return $this->belongsTo(IssueItem::class, 'issue_item_id','id');
    }

    public function itemSerial()
    {
        return $this->belongsTo(ItemSerial::class, 'item_serial_id','id');
    }
}
