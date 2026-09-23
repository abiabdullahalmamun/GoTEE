<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FooterLink extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'footer_links';
    protected $fillable = [
        'title',
        'link_url',
        'created_by',
        'updated_by',
        'updated_by',
    ];
}
