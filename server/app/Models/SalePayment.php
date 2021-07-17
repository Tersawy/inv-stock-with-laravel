<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalePayment extends Model
{
    protected $fillable = ['amount', 'payment_method', 'note', 'sale_id'];
}
