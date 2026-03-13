<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemUom extends Model
{     protected $primaryKey = 'item_uom_id';
      protected $fillable = [
        'item_uom_name'
    ];

    public function item(){
        return $this->hasMany(Item::class, 'item_uom_id');
    }
}
