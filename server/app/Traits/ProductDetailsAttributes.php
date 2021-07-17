<?php

namespace App\Traits;

use App\Helpers\Constants;
use App\Models\Product;
use Illuminate\Support\Arr;

trait ProductDetailsAttributes
{

  public function getUnitCostAttribute()
  {
    return $this->getUnitValue('cost');
  }


  public function getUnitPriceAttribute()
  {
    return $this->getUnitValue('price');
  }


  public function getNetCostAttribute()
  {
    return $this->getNetValue('cost');
  }


  public function getNetPriceAttribute()
  {
    return $this->getNetValue('price');
  }


  public function getTaxCostAttribute()
  {
    return $this->getTaxValue('cost');
  }


  public function getTaxPriceAttribute()
  {
    return $this->getTaxValue('price');
  }


  public function getTotalCostAttribute()
  {
    return $this->getTotalValue('cost');
  }


  public function getTotalPriceAttribute()
  {
    return $this->getTotalValue('price');
  }


  private function getGrandTotalCost()
  {
    return $this->getGrandTotalValue('cost');
  }

  private function getGrandTotalPrice()
  {
    return $this->getGrandTotalValue('price');
  }


  private function getNetValue($attrName)
  {
    $value = $this->getValueExcludingDiscount($attrName);

    if ($this->attributes['tax_method'] === Constants::TAX_INCLUSIVE) {
      $value = $value - $this->getTaxValue($attrName);
    }

    return $value;
  }


  private function getTotalValue($attrName)
  {
    return $this->getNetValue($attrName) + $this->getTaxValue($attrName);
  }


  public function getGrandTotalValue($attrName)
  {
    return $this->getTotalValue($attrName) * $this->quantity;
  }


  public function details()
  {
    $this->append($this->getMutatedAttributes());

    return [
      'id'            => $this->id,
      'instock'       => $this->instock,
      'unit_cost'     => $this->unit_cost,
      'unit_price'    => $this->unit_price,
      'net_cost'      => $this->net_cost,
      'net_price'     => $this->net_price,
      'tax'           => $this->tax,
      'tax_method'    => $this->tax_method,
      'tax_cost'      => $this->tax_cost,
      'tax_price'     => $this->tax_price,
      'total_cost'    => $this->total_cost,
      'total_price'   => $this->total_price,
      'purchase_unit' => $this->purchase_unit->short_name,
      'sale_unit'     => $this->sale_unit->short_name
    ];
  }


  private function getDiscount($attrName)
  {
    $discount = Arr::get($this, 'discount', 0);

    $method = Arr::get($this, 'discount_method', 0);

    if ($method == Constants::DISCOUNT_FIXED) {
      return $discount;
    }

    return $discount * ($this->getUnitValue($attrName) / 100);
  }


  private function getValueExcludingDiscount($attrName)
  {
    return $this->getUnitValue($attrName) - $this->getDiscount($attrName);
  }


  private function getUnitValue($attrName) // cost or price
  {
    $value = $this->attributes[$attrName];

    if (!$this->isProductModal()) {
      return $value;
    }

    $unit = $attrName == 'cost' ? 'purchase_unit' : 'sale_unit';

    $value = $value / $this->{$unit}->value;

    return $value;
  }


  private function getTaxValue($attrName)
  {
    $unitValue = $this->getValueExcludingDiscount($attrName); // cost or price

    if ($this->attributes['tax_method'] === Constants::TAX_INCLUSIVE) {

      $taxDivisor = 1 + ($this->attributes['tax'] / 100);

      $valueBeforeTax = $unitValue / $taxDivisor;

      $taxAmount = $unitValue - $valueBeforeTax;

      return $taxAmount;
    }

    $onePercentOfValue = $unitValue / 100;

    $taxAmount = $onePercentOfValue * $this->attributes['tax'];

    return $taxAmount;
  }


  private function isProductModal()
  {
    return static::class === Product::class;
  }


  public function product()
  {
    return $this->belongsTo(Product::class);
  }
}


    // if ($product->purchase_unit->operator === '*') {
    //   $value = $value * $product->purchase_unit->value;
    // }

    // if ($product->purchase_unit->operator === '/') {
    //   $value = $value / $product->purchase_unit->value;
    // }


  // public function getNetCostAttribute()
  // {
  //   $cost = $this->attributes['cost'];

  //   if ($this->attributes['tax_method'] === Product::TAX_INCLUSIVE) {

  //     $onePercentOfCost = $cost / 100;

  //     $taxAmount = $onePercentOfCost * $this->attributes['tax_method'];

  //     $cost = $cost - $taxAmount;
  //   }

  //   $isProductModel = static::class === Product::class;

  //   $product = $isProductModel ? $this : $this->attributes['product'];

  //   if ($product->purchase_unit->operator === '*') {
  //     $cost = $cost * $product->purchase_unit->value;
  //   }

  //   if ($product->purchase_unit->operator === '/') {
  //     $cost = $cost / $product->purchase_unit->value;
  //   }

  //   return $cost;
  // }


  // public function getNetCostAttribute()
  // {
  //   $cost = $this->attributes['cost'];

  //   if ($this->attributes['tax_method'] === Product::TAX_INCLUSIVE) {

  //     $onePercentOfCost = $cost / 100;

  //     $taxAmount = $onePercentOfCost * $this->attributes['tax_method'];

  //     $cost = $cost - $taxAmount;
  //   }

  //   $isProductModel = static::class === Product::class;

  //   $product = $isProductModel ? $this : $this->attributes['product'];

  //   if ($product->purchase_unit->operator === '*') {
  //     $cost = $cost * $product->purchase_unit->value;
  //   }

  //   if ($product->purchase_unit->operator === '/') {
  //     $cost = $cost / $product->purchase_unit->value;
  //   }

  //   return $cost;
  // }

  // public function getTaxCostAttribute()
  // {
  //   if ($this->attributes['tax_method'] === Product::TAX_INCLUSIVE) {

  //     $onePercentOfCost = $this->attributes['cost'] / 100;

  //     $taxAmount = $onePercentOfCost * $this->attributes['tax_method'];
  //   }


  //   $cost = $this->attributes['cost'];

  //   if ($this->attributes['tax_method'] === Product::TAX_INCLUSIVE) {

  //     $onePercentOfCost = $cost / 100;

  //     $taxAmount = $onePercentOfCost * $this->attributes['tax_method'];

  //     $cost = $cost - $taxAmount;
  //   }

  //   return $this->getNetCostAttribute();
  //   return $this->attributes['net_cost'];
  //   return $cost;
  // }
