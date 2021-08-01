<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleReturnPayment extends Model
{
    protected $fillable = ['reference', 'amount', 'payment_method', 'note', 'sale_return_id'];
}
