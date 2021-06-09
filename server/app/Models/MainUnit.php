<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainUnit extends Model
{
    protected $fillable = ["name", "short_name", "value", "operator"];

    public function sub_units()
    {
        return $this->hasMany(SubUnit::class);
    }
}
