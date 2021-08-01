<?php

namespace App\Models;

use App\Traits\InvoiceAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
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
        'payment_status',
        'date',
        'total_price',
        'paid',
        'is_pos'
    ];

    protected $casts = ['is_pos' => 'boolean'];

    protected $fieldActionName = 'price';

    public function details()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function payments()
    {
        return $this->hasMany(SalePayment::class);
    }
}
