<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [ 'name', 'item_code', 'type_id', 'uom_id', 'category_id','brand_id', 'description','purchase_price','sell_price','warranty_month','min_record',
        'default_variant_id','default_image_id', 'video_url', 'sequence', 'is_active', 'created_by', 'updated_by'
    ];

    public function images()
    {
        return $this->hasMany(ItemImage::class);
    }

    public function firstImage()
    {
        return $this->hasOne(ItemImage::class)->orderBy('id');
    }

    public function lastStock()
    {
        return $this->hasOne(StockItem::class)
            ->where(function ($query) {
                if (Auth::check()) {
                    $query->where('store_id', Auth::user()->store_id);
                }
            })
            ->orderBy('id','DESC');
    }

    public function lastStockPerStore($itemId)
    {
        return $this->hasMany(StockItem::class)
            ->where('item_id', $itemId)
            ->whereIn('id', function ($query) use ($itemId) {
                $query->selectRaw('MAX(id)')
                    ->from('stock_items')
                    ->where('item_id', $itemId) // Explicitly filter by the item ID
                    ->groupBy('store_id');
            });

    }





    public function serials()
    {
        return $this->hasMany(ItemSerial::class)
            ->where(function ($query) {
                if (Auth::check()) {
                    $query->where('store_id', Auth::user()->store_id);
                }
            })
            ->where('is_stock', 1);
    }

    public function variants()
    {
        return $this->hasMany(ItemVariant::class);
    }

    public function defaultVariants()
    {
        return $this->hasOne(ItemVariant::class, 'id', 'default_variant_id');
    }
    public function defaultImages()
    {
        return $this->hasOne(ItemImage::class, 'id', 'default_image_id');
    }

    public function attributes()
    {
        return $this->hasMany(ItemAttribute::class);
    }

    public function category()
    {
        return $this->belongsTo(HkProdCategory::class,'category_id','id');
    }

    public function brand()
    {
        return $this->belongsTo(ItemBrand::class,'brand_id','id');
    }

    public function itemType()
    {
        return $this->belongsTo(ItemType::class,'type_id','id');
    }
    public function itemUOM()
    {
        return $this->belongsTo(Uom::class,'uom_id','id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items');
    }
}
