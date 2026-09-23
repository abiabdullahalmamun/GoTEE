<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    use HasFactory;

    protected $fillable = [
        'receive_return_id',
        'trans_date',
        'trans_no',
        'challan_no',
        'challan_date',
        'trans_type_id',
        'store_id',
        'customer_id',
        'sub_total_amount',
        'vat_percent',
        'vat_amount',
        'total_amount',
        'total_discount',
        'total_payable',
        'paid_amount',
        'due_amount',
        'remark',
        'created_by','updated_by','created_at','updated_at'
    ];


    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id','id');
    }

    public function store(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id','id');
    }

    public function transType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TransType::class, 'trans_type_id','id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by','id');
    }


    public function issueItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(IssueItem::class,'issue_id','id');
    }


}
