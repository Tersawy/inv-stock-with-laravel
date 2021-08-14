<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\InvoiceDetailsAttributes;

class AdjustmentDetail extends Model
{
    use InvoiceDetailsAttributes;

    protected $fillable = ['quantity', 'type', 'variant_id', 'product_id', 'adjustment_id'];
}
