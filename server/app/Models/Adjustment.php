<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Adjustment extends Model
{
    use SoftDeletes;

    protected $fillable = ['items_count', 'note', 'date', 'warehouse_id'];
}
