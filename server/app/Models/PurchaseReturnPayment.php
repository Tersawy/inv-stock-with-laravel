<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturnPayment extends Model
{
    protected $fillable = ['amount', 'payment_method', 'note', 'purchase_return_id'];
}
