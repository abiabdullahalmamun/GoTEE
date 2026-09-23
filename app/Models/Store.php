<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;

class Store extends Model
{
    use HasFactory;


    protected $fillable = ['code', 'name', 'name', 'address'];

    protected $hidden = ['created_at','updated_at'];

    public function companyInfo()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
