<?php

namespace App\Models;

use App\Traits\ProductDetailsAttributes;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    use ProductDetailsAttributes;

    protected $fillable = [
        'product_id',
        'purchase_id',
        'cost',
        'tax',
        'tax_method',
        'discount',
        'discount_method',
        'quantity'
    ];
}
