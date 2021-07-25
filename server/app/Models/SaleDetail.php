<?php

namespace App\Models;

use App\Traits\ProductDetailsAttributes;
use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    use ProductDetailsAttributes;

    protected $fillable = [
        'product_id',
        'sale_id',
        'variant_id',
        'price',
        'tax',
        'tax_method',
        'discount',
        'discount_method',
        'quantity'
    ];
}
