<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ProductDetailsAttributes;

class TransferDetail extends Model
{
    use ProductDetailsAttributes;

    protected $fillable = [
        'product_id',
        'transfer_id',
        'variant_id',
        'cost',
        'tax',
        'tax_method',
        'discount',
        'discount_method',
        'quantity'
    ];
}
