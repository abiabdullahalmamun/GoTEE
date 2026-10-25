<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpnCodeBank extends Model
{
    protected $table = 'tbl_operationCode';
    protected $guarded = [];


    public function nodeIncentive()
    {
        return $this->hasMany(Incentive::class,'OprNo' ,'processID'); // 'member_id' is the foreign key in the transactions table
    }

    public function nodedataprocess()
    {
        return $this->hasMany(NodeDataProcessed::class,'OprNo','processID'); // 'member_id' is the foreign key in the transactions table
    }
}
