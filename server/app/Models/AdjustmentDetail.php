<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdjustmentDetail extends Model
{
    protected $fillable = ['quantity', 'type', 'variant_id', 'product_id', 'adjustment_id'];
}
