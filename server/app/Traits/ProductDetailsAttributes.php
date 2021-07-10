<?php

namespace App\Traits;

use App\Models\Product;

trait ProductDetailsAttributes
{

  public function getUnitCostAttribute()
  {
    return $this->getValueByUnitOperator('cost');
  }


  public function getUnitPriceAttribute()
  {
    return $this->getValueByUnitOperator('price');
  }


  public function getNetCostAttribute()
  {
    $cost = $this->getUnitCostAttribute();

    if ($this->attributes['tax_method'] === Product::TAX_INCLUSIVE) {
      $cost = $cost - $this->getTaxAmountByField('cost');
    }

    return $cost;
  }


  public function getNetPriceAttribute()
  {
    $price = $this->getUnitPriceAttribute();

    if ($this->attributes['tax_method'] === Product::TAX_INCLUSIVE) {
      $price = $price - $this->getTaxAmountByField('price');
    }

    return $price;
  }


  public function getTaxCostAttribute()
  {
    return $this->getTaxAmountByField('cost');
  }


  public function getTaxPriceAttribute()
  {
    return $this->getTaxAmountByField('price');
  }


  public function getTotalCostAttribute()
  {
    return $this->getNetCostAttribute() + $this->getTaxAmountByField('cost');
  }


  public function getTotalPriceAttribute()
  {
    return $this->getNetPriceAttribute() + $this->getTaxAmountByField('price');
  }


  public function details()
  {
    $this->thisProduct()->append($this->thisProduct()->getMutatedAttributes());

    return [
      'id'            => $this->thisProduct()->id,
      'instock'       => $this->thisProduct()->instock,
      'unit_cost'     => $this->thisProduct()->unit_cost,
      'unit_price'    => $this->thisProduct()->unit_price,
      'net_cost'      => $this->thisProduct()->net_cost,
      'net_price'     => $this->thisProduct()->net_price,
      'tax'           => $this->thisProduct()->tax,
      'tax_method'    => $this->thisProduct()->tax_method,
      'tax_cost'      => $this->thisProduct()->tax_cost,
      'tax_price'     => $this->thisProduct()->tax_price,
      'total_cost'    => $this->thisProduct()->total_cost,
      'total_price'   => $this->thisProduct()->total_price,
      'purchase_unit' => $this->thisProduct()->purchase_unit->short_name,
      'sale_unit'     => $this->thisProduct()->sale_unit->short_name
    ];
  }


  protected function getValueByUnitOperator($attrName) // cost or price
  {
    $value = $this->attributes[$attrName];

    $product = $this->thisProduct();

    $unit = $attrName == 'cost' ? 'purchase_unit' : 'sale_unit';

    $value = $value / $product->{$unit}->value;

    return $value;
  }


  protected function getTaxAmountByField($attrName)
  {
    $unitValue = $this->getValueByUnitOperator($attrName); // cost or price

    if ($this->attributes['tax_method'] === Product::TAX_INCLUSIVE) {

      $taxDivisor = 1 + ($this->attributes['tax'] / 100);

      $valueBeforeTax = $unitValue / $taxDivisor;

      $taxAmount = $unitValue - $valueBeforeTax;

      return $taxAmount;
    }

    $onePercentOfValue = $unitValue / 100;

    $taxAmount = $onePercentOfValue * $this->attributes['tax'];

    return $taxAmount;
  }


  protected function thisProduct()
  {
    $isProductModel = static::class === Product::class;

    $product = $isProductModel ? $this : $this->attributes['product'];

    return $product;
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
