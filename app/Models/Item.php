<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
     protected $primaryKey = 'item_id';
    protected $fillable = [
        'item_name',
        'item_serialno',
        'item_quantity',
        'item_remark',
        'item_category_id',
        'item_brand_id',
        'item_uom_id',
    ];

    public function brand()
    {
         return $this->belongsTo(ItemBrand::class,'item_brand_id','item_brand_id');
    }

    public function category()
    {
        return $this->belongsTo(ItemCategory::class, 'item_category_id');
    }

    public function uom()
    {
        return $this->belongsTo(ItemUom::class, 'item_uom_id');
    }
}
