<?php

namespace App\Models;

use App\Models\PurchaseReturnPayment;
use App\Traits\InvoiceAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseReturn extends Model
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
        'total_price',
        'date'
    ];

    protected $fieldActionName = 'cost';

    public function details()
    {
        return $this->hasMany(PurchaseReturnDetail::class);
    }

    public function payments()
    {
        return $this->hasMany(PurchaseReturnPayment::class);
    }
}
