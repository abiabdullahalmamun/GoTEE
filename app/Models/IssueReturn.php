<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueReturn extends Model
{
    use HasFactory;

    protected $fillable = [
            'issue_id',
            'trans_date',
            'return_date',
            'return_no',
            'store_id',
            'total_return_amount',
            'return_paid_amount',
            'return_due_amount',
            'reason',
            'created_by',
            'created_at',
            'updated_by',
            'updated_at',
    ];

    public function store(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id','id');
    }


    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by','id');
    }

    public function issueRef(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Issue::class, 'issue_id','id');
    }


    public function issueReturnItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(IssueReturnItem::class,'issue_return_id','id');
    }


}
