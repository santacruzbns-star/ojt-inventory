<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemCategory extends Model
{
     protected $primaryKey = 'item_category_id';
    protected $fillable = [
        'item_category_name',
    ];

    public function items()
    {
        return $this->hasMany(Item::class,'item_category_id','item_category_id');
    }
}
