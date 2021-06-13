<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use SoftDeletes;

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
}
