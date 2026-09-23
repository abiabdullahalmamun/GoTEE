<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CounterSvc extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_counter_svc';
    protected $fillable = ['counterId', 'svcId','created_by'];



    public function counter()
    {
        return $this->belongsTo(Counter::class, 'counterId');
    }
    public function service()
    {
        return $this->belongsTo(Service::class, 'svcId');
    }
 
}
