<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\InvoiceDetailsAttributes;

class PurchaseDetail extends Model
{
    use InvoiceDetailsAttributes;

    protected $fillable = [
        'product_id',
        'purchase_id',
        'variant_id',
        'cost',
        'tax',
        'tax_method',
        'discount',
        'discount_method',
        'quantity'
    ];
}
