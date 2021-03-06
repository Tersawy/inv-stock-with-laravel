<?php

namespace App\Traits;

use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Helpers\Constants;

trait InvoiceAttributes
{
  public function supplier()
  {
    return $this->belongsTo(Supplier::class);
  }


  public function customer()
  {
    return $this->belongsTo(Customer::class);
  }


  public function warehouse()
  {
    return $this->belongsTo(Warehouse::class);
  }


  public function getDueAttribute()
  {
    return $this->total_price - $this->paid;
  }


  public function getNewPaymentStatusAttribute()
  {
    $total = $this->total_price;

    $due = $this->getDueAttribute();

    if ($due === $total) return Constants::PAYMENT_STATUS_UNPAID;

    if ($due <= 0) return Constants::PAYMENT_STATUS_PAID;

    // if ($due > 0 && $due < $total) return "Partial";
    return Constants::PAYMENT_STATUS_PARTIAL;
  }


  private function getTotalPriceOfDetails()
  {
    $total = 0;

    $this->details->each(function ($d) use (&$total) {
      $total += $d->getGrandTotalValue($this->fieldActionName);
    });

    return $total;
  }


  private function getDiscountAmount()
  {
    if ($this->discount_method == Constants::DISCOUNT_FIXED) {
      return $this->discount;
    }

    return $this->discount * ($this->getTotalPriceOfDetails() / 100);
  }


  private function getTotalExcludingDiscount()
  {
    return $this->getTotalPriceOfDetails() - $this->getDiscountAmount();
  }


  private function getTaxValue()
  {
    $totalWithoutDiscount = $this->getTotalExcludingDiscount();

    $onePercentOfValue = $totalWithoutDiscount / 100;

    $taxAmount = $onePercentOfValue * $this->tax;

    return $taxAmount;
  }
}
