<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    const PAYMENT_CASH          = 0;
    const PAYMENT_CHEQUE        = 1;
    const PAYMENT_CREDIT_CARD   = 2;
    const PAYMENT_WESTREN_UNION = 3;
    const PAYMENT_BANK_TRANSFER = 4;

    const PAYMENT_METHODS = [
        PurchasePayment::PAYMENT_CASH,
        PurchasePayment::PAYMENT_CHEQUE,
        PurchasePayment::PAYMENT_CREDIT_CARD,
        PurchasePayment::PAYMENT_WESTREN_UNION,
        PurchasePayment::PAYMENT_BANK_TRANSFER
    ];
}
