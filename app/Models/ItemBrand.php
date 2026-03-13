<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemBrand extends Model
{
    protected $primaryKey = 'item_brand_id';
    protected $fillable = [
        'item_brand_name'
    ];

    public function item(){
        return $this->hasMany(Item::class, 'item_brand_id');
    }
}
