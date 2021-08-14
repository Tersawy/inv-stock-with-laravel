<?php

namespace App\Traits;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductVariantImage;

trait InvoiceDetailsAttributes
{
  public function product()
  {
    return $this->belongsTo(Product::class);
  }

  public function variant()
  {
    return $this->belongsTo(ProductVariant::class);
  }

  public function productImage()
  {
    return $this->belongsTo(ProductImage::class, 'product_id', 'product_id')->where('default', 1)->withDefault(['name' => 'product_empty.jpeg']);
  }

  public function variantImage()
  {
    return $this->belongsTo(ProductVariantImage::class, 'variant_id', 'variant_id')->where('default', 1)->withDefault(['name' => 'product_empty.jpeg']);
  }

  public function image()
  {
    if (is_null($this->variant_id)) {
      return $this->productImage();
    }

    return $this->variantImage();
  }
}
