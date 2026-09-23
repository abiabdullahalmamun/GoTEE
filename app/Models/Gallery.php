<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use Loggable;
    use HasFactory;

    protected $fillable = [
        'image_url',
        'text',
        'created_by',
        'updated_by',
    ];
}
