<?php

namespace  App\Models;

use Illuminate\Database\Eloquent\Model;

class Buyer extends Model
{
    protected $table = 'tbl_buyer';
    protected $fillable = [ 'buyer', 'status','created_by','created_at','updated_at' ];

    protected $guarded = [];


    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
