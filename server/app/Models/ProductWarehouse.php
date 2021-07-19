<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductWarehouse extends Model
{
    protected $fillable = ['product_id', 'warehouse_id', 'product_variant_id', 'instock'];
}
