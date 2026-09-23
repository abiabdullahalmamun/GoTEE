<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'sub_title','active_at','inactive_at', 'description','banner_url','is_active','created_by','updated_by'];

    protected $hidden = ['created_at','updated_at'];


}
