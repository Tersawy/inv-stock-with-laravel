<?php

namespace App\Helpers;

class Constants
{
  const PURCHASE_RECEIVED = 0;
  const PURCHASE_PENDING  = 1;
  const PURCHASE_ORDERED  = 2;
  const PURCHASE_STATUS   = [Constants::PURCHASE_RECEIVED, Constants::PURCHASE_PENDING, Constants::PURCHASE_ORDERED];


  const PURCHASE_RETURN_COMPLETED = 0;
  const PURCHASE_RETURN_PENDING   = 1;
  const PURCHASE_RETURN_STATUS    = [Constants::PURCHASE_RETURN_COMPLETED, Constants::PURCHASE_RETURN_PENDING];


  const SALE_COMPLETED = 0;
  const SALE_PENDING   = 1;
  const SALE_ORDERED   = 2;
  const SALE_STATUS    = [Constants::SALE_COMPLETED, Constants::SALE_PENDING, Constants::SALE_ORDERED];


  const SALE_RETURN_RECEIVED = 0;
  const SALE_RETURN_PENDING  = 1;
  const SALE_RETURN_STATUS   = [Constants::SALE_RETURN_RECEIVED, Constants::SALE_RETURN_PENDING];


  const QUOTATION_SENT    = 0;
  const QUOTATION_PENDING = 1;
  const QUOTATION_STATUS  = [Constants::QUOTATION_SENT, Constants::QUOTATION_PENDING];


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


  const PAYMENT_STATUS_PAID     = 0;
  const PAYMENT_STATUS_UNPAID   = 1;
  const PAYMENT_STATUS_PARTIAL  = 2;

  const PAYMENT_STATUS = [
    Constants::PAYMENT_STATUS_PAID,
    Constants::PAYMENT_STATUS_UNPAID,
    Constants::PAYMENT_STATUS_PARTIAL
  ];
}
