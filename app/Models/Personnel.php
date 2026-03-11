<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Personnel extends Model
{
       protected $fillable = [
         'branch_id',
         'personnel_name'
       
    ];

    public function branch(){
        return $this->hasOne(Branch::class);
    }
}

