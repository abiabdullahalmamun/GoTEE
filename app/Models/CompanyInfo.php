<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyInfo extends Model
{
    use HasFactory;
    use Loggable;

    protected $table = 'company_info';
    protected $fillable = [
        'title',
        'company_name',
        'about_us',
        'address',
        'phone',
        'email',
        'logo_url',
        'signature_url',
        'signature_url2',
        'pdf_url',
        'created_by',
        'updated_by',
        'updated_by',
    ];
}
