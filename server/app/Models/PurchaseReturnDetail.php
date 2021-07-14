<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ProductDetailsAttributes;

class PurchaseReturnDetail extends Model
{
    use ProductDetailsAttributes;

    protected $fillable = [
        'product_id',
        'purchase_return_id',
        'cost',
        'tax',
        'tax_method',
        'discount',
        'discount_method',
        'quantity'
    ];
}
