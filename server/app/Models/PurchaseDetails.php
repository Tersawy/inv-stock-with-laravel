<?php

namespace App\Models;

use App\Traits\ProductDetailsAttributes;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetails extends Model
{
    use ProductDetailsAttributes;

    protected $fillable = [
        'product_id',
        'cost',
        'tax',
        'tax_method',
        'discount',
        'discount_method',
        'quantity'
    ];
}
