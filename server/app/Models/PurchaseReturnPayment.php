<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturnPayment extends Model
{
    protected $fillable = ['reference', 'amount', 'payment_method', 'note', 'purchase_return_id'];
}
