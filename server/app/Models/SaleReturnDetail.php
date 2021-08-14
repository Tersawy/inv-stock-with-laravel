<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\InvoiceDetailsAttributes;

class SaleReturnDetail extends Model
{
    use InvoiceDetailsAttributes;

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
