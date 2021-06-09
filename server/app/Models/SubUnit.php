<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubUnit extends Model
{
    protected $fillable = ['name', 'short_name', 'value', 'operator', 'main_unit_id'];

    public function main_unit()
    {
        return $this->belongsTo(MainUnit::class);
    }
}
