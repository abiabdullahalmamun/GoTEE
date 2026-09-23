<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'start_at', 'end_at'];

    protected $hidden = ['created_at','updated_at'];


}
