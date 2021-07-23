<?php

namespace App\Models;

use App\Traits\InvoiceAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use SoftDeletes, InvoiceAttributes;

    protected $fillable = [
        'warehouse_id',
        'customer_id',
        'tax',
        'discount',
        'discount_method',
        'status',
        'shipping',
        'note',
        'date'
    ];

    protected $append = ['reference'];

    protected $fieldActionName = 'price';

    public function details()
    {
        return $this->hasMany(QuotationDetail::class);
    }

    public function getReferenceAttribute()
    {
        return 'QT_' . (1110 + $this->id);
    }
}
