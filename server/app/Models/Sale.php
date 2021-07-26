<?php

namespace App\Models;

use App\Traits\InvoiceAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
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
        'payment_status',
        'date',
        'is_pos'
    ];

    protected $casts = [ 'is_pos' => 'boolean' ];

    protected $append = ['reference'];

    protected $fieldActionName = 'price';

    public function details()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function payments()
    {
        return $this->hasMany(SalePayment::class);
    }

    public function getReferenceAttribute()
    {
        return 'SL_' . (1110 + $this->id);
    }
}
