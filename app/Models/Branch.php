<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $primaryKey = 'branch_id';
    protected $fillable = [
        'branch_name',
        'branch_department'
    ];

    public function personnel(){
        return $this->hasMany(Personnel::class);
    }
}
