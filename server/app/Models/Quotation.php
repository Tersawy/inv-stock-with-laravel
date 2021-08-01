<?php

namespace App\Models;

use App\Traits\InvoiceAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use SoftDeletes, InvoiceAttributes;

    protected $fillable = [
        'reference',
        'warehouse_id',
        'customer_id',
        'tax',
        'discount',
        'discount_method',
        'status',
        'shipping',
        'note',
        'total_price',
        'date'
    ];

    protected $fieldActionName = 'price';

    public function details()
    {
        return $this->hasMany(QuotationDetail::class);
    }
}
