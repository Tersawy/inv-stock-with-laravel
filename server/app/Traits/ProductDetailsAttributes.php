<?php

namespace App\Traits;

use App\Models\Product;

trait ProductDetailsAttributes
{

  public function getNet_costAttribute()
  {
    $cost = $this->attributes['cost'];

    if ($this->attributes['tax_method'] === Product::TAX_INCLUSIVE) {

      $onePercentOfCost = $cost / 100;

      $taxAmount = $onePercentOfCost * $this->attributes['tax_method'];

      $cost = $cost - $taxAmount;
    }

    $product = $this->attributes['product'];

    if ($product->purchase_unit->operator === "*") {
      $cost = $cost * $product->purchase_unit->value;
    }

    if ($product->purchase_unit->operator === "/") {
      $cost = $cost / $product->purchase_unit->value;
    }

    return $cost;
  }

  public function getTax_costAttribute()
  {
    $cost = $this->attributes['cost'];

    if ($this->attributes['tax_method'] === Product::TAX_INCLUSIVE) {

      $onePercentOfCost = $cost / 100;

      $taxAmount = $onePercentOfCost * $this->attributes['tax_method'];

      $cost = $cost - $taxAmount;
    }

    return $cost;
  }


  public function getTax_amountAttribute()
  {
    if ($this->attributes['tax_method'] === Product::TAX_EXCLUSIVE) {
    }
  }


  public function product()
  {
    return $this->belongsTo(Product::class);
  }
}
