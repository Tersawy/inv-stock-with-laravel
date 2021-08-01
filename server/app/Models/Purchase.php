<?php

namespace App\Models;

use App\Models\PurchasePayment;
use App\Traits\InvoiceAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use SoftDeletes, InvoiceAttributes;

    protected $fillable = [
        'reference',
        'warehouse_id',
        'supplier_id',
        'tax',
        'discount',
        'discount_method',
        'status',
        'shipping',
        'note',
        'payment_status',
        'date'
    ];

    protected $fieldActionName = 'cost';

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function payments()
    {
        return $this->hasMany(PurchasePayment::class);
    }
}
