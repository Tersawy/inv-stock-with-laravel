<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    protected $fillable = ['amount', 'payment_method', 'note', 'purchase_id'];
}
