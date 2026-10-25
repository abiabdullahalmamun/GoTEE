<?php

namespace  App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderNo extends Model
{
    protected $table = 'tbl_order_no';
    protected $fillable = [ 'OrderCode','OrderNo', 'attr',  'Style', 'buyer', 'status','created_by','created_at','updated_at' , 'totalsmv','item' ];

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function buyers()
    {
        return $this->belongsTo(Buyer::class,  'buyer');
    }

    public function nodeIncentive()
    {
        return $this->hasMany(Incentive::class, 'OrderCode','OrderCode'); // 'member_id' is the foreign key in the transactions table
    }
}
