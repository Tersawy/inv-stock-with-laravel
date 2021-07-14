<?php

namespace App\Models;

use App\Models\PurchaseReturnPayment;
use App\Traits\InvoiceAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseReturn extends Model
{
    use SoftDeletes, InvoiceAttributes;

    const COMPLETED = 0;
    const PENDING   = 1;
    const STATUS = [PurchaseReturn::PENDING, PurchaseReturn::COMPLETED];

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
        return $this->hasMany(PurchaseReturnDetail::class);
    }

    public function payments()
    {
        return $this->hasMany(PurchaseReturnPayment::class);
    }

    public function getReferenceAttribute()
    {
        return 'RT_' . (1110 + $this->id);
    }
}
