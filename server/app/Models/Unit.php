<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = ['name', 'short_name', 'value', 'operator', 'main_unit_id'];

    public function main_unit()
    {
        return $this->belongsTo(Unit::class, 'id', 'main_unit_id');
    }

    public function sub_units()
    {
        return $this->hasMany(Unit::class, 'main_unit_id');
    }
}
