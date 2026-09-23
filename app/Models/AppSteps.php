<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSteps extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_app_steps';
    protected $fillable = ['Step','created_by'];

    /**
     * Get the parent menu.
     */
}
