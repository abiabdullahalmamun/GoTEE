<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormFill extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_form_fill';
    protected $fillable = [ 'centerId' ,'webfile', 'passport', 'Name', 'contact','Date','created_by','fee','day_sl','remarks'];

    /**
     * Get the parent menu.
     */

    public function center()
    {
        return $this->belongsTo(Center::class, 'centerId');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


}
