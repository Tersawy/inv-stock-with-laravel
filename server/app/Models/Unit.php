<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'short_name', 'value', 'operator', 'main_unit_id'];

    public function main_unit()
    {
        return $this->belongsTo(Unit::class, 'main_unit_id', 'id');
    }

    public function sub_units()
    {
        return $this->hasMany(Unit::class, 'main_unit_id');
    }
}
