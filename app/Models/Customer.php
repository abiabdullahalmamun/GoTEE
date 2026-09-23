<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'phone','email', 'image_url','address','is_active'];

    protected $hidden = ['created_at','updated_at'];



}
