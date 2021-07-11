<?php

namespace App\Models;

use App\Traits\InvoiceAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use SoftDeletes, InvoiceAttributes;

    const RECEIVED  = 0;
    const PENDING   = 1;
    const ORDERED   = 2;
    const STATUS = [Purchase::RECEIVED, Purchase::PENDING, Purchase::ORDERED];

    protected $fillable = [
        'warehouse_id',
        'supplier_id',
        'tax',
        'discount',
        'discount_method',
        'status',
        'shipping',
        'note',
        'date'
    ];

    protected $append = ['reference'];

    protected $fieldActionName = 'cost';

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function getReferenceAttribute()
    {
        return 'PR_' . (1110 + $this->id);
    }
}
