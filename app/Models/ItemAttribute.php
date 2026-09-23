<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemAttribute extends Model
{
    use HasFactory;

    protected $fillable = ['item_id', 'name', 'description', 'sequence','created_by','updated_by','created_at','updated_at'];

}
