<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    protected $fillable = ['reference', 'amount', 'payment_method', 'note', 'purchase_id'];
}
