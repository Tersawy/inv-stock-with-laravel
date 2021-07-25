<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ProductDetailsAttributes;

class SaleReturnDetail extends Model
{
    use ProductDetailsAttributes;

    protected $fillable = [
        'product_id',
        'sale_return_id',
        'variant_id',
        'price',
        'tax',
        'tax_method',
        'discount',
        'discount_method',
        'quantity'
    ];
}
