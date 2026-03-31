<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Personnel extends Model
{
       protected $primaryKey = 'personnel_id';
       protected $fillable = [
              'branch_id',
              'personnel_name'

       ];

       public function branch()
       {
              return $this->belongsTo(Branch::class, 'branch_id', 'branch_id');
       }

       public function personnelItems()
       {
              return $this->hasMany(PersonnelItem::class, 'personnel_id', 'personnel_id');
       }
}

