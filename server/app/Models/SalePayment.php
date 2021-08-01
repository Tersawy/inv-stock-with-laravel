<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalePayment extends Model
{
    protected $fillable = ['reference', 'amount', 'payment_method', 'note', 'sale_id'];
}
