<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductWarehouse extends Model
{
    use SoftDeletes;

    protected $fillable = ['product_id', 'warehouse_id', 'product_variant_id', 'instock'];
}
