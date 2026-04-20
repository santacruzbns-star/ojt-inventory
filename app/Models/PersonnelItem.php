<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonnelItem extends Model
{
    protected $primaryKey = 'personnel_item_id';

    protected $fillable = [
        'item_id',
        'personnel_id',
        'personnel_item_quantity',
        'personnel_date_receive',
        'personnel_date_issued',
        'personnel_item_remarks',
        'item_remark',
        'return_reason_preset',
        'return_reason_detail',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'item_id');
    }

    public function personnel()
    {
        return $this->belongsTo(Personnel::class, 'personnel_id', 'personnel_id');
    }

     public function personnelItem()
    {
        return $this->belongsTo(PersonnelItem::class, 'personnel_item_id', 'personnel_item_id');
    }
}