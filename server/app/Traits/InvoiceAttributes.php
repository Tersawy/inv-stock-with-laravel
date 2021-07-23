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


  public function getGrandTotalAttribute()
  {
    return $this->getTotalExcludingDiscount() + $this->getTaxValue() + $this->shipping;
  }


  public function getPaidAttribute()
  {
    $total = 0;

    $this->payments->each(function ($p) use (&$total) {
      $total += $p->amount;
    });

    return $total;
  }


  public function getDueAttribute()
  {
    return $this->getGrandTotalAttribute() - $this->getPaidAttribute();
  }


  // public function getPaymentStatusAttribute()
  // {
  //   $total = $this->getGrandTotalAttribute();

  //   $due = $this->getDueAttribute();

  //   if ($due === $total) return "Unpaid";

  //   if ($due <= 0) return "Paid";

  //   // if ($due > 0 && $due < $total) return "Partial";
  //   return "Partial";
  // }


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
