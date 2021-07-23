<?php

namespace App\Helpers;

class Constants
{
  const INVOICE_RECEIVED  = 0;
  const INVOICE_PENDING   = 1;
  const INVOICE_ORDERED   = 2;
  const INVOICE_STATUS    = [Constants::INVOICE_RECEIVED, Constants::INVOICE_PENDING, Constants::INVOICE_ORDERED];


  const INVOICE_RETURN_COMPLETED = 0;
  const INVOICE_RETURN_PENDING   = 1;
  const INVOICE_RETURN_STATUS    = [Constants::INVOICE_RETURN_COMPLETED, Constants::INVOICE_RETURN_PENDING];


  const TAX_EXCLUSIVE = 0;
  const TAX_INCLUSIVE = 1;
  const TAX_METHODS   = [Constants::TAX_EXCLUSIVE, Constants::TAX_INCLUSIVE];


  const DISCOUNT_FIXED    = 0;
  const DISCOUNT_PERCENT  = 1;
  const DISCOUNT_METHODS  = [Constants::DISCOUNT_FIXED, Constants::DISCOUNT_PERCENT];


  const PAYMENT_CASH          = 0;
  const PAYMENT_CHEQUE        = 1;
  const PAYMENT_CREDIT_CARD   = 2;
  const PAYMENT_WESTREN_UNION = 3;
  const PAYMENT_BANK_TRANSFER = 4;


  const PAYMENT_METHODS = [
    Constants::PAYMENT_CASH,
    Constants::PAYMENT_CHEQUE,
    Constants::PAYMENT_CREDIT_CARD,
    Constants::PAYMENT_WESTREN_UNION,
    Constants::PAYMENT_BANK_TRANSFER
  ];


  const PAYMENT_STATUS_PAID = 0;
  const PAYMENT_STATUS_UNPAID = 1;
  const PAYMENT_STATUS_PARTIAL = 2;

  const PAYMENT_STATUS = [
    Constants::PAYMENT_STATUS_PAID,
    Constants::PAYMENT_STATUS_UNPAID,
    Constants::PAYMENT_STATUS_PARTIAL
  ];
}
