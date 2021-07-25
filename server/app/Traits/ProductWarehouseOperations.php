<?php

namespace App\Traits;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\ProductVariant;
use App\Models\ProductWarehouse;

trait ProductWarehouseOperations
{
  function addProductToWarehouses(Product $product)
  {
    $warehouses = Warehouse::all();

    if (!count($warehouses)) return;

    $variants = $product->has_variants ? $product->variants : [];

    $product_warehouse = [];

    foreach ($warehouses as $warehouse) {

      $product_warehouse[] = ['product_id' => $product->id, 'warehouse_id' => $warehouse->id, 'variant_id' => null];

      if (!count($variants)) continue;

      foreach ($variants as $variant) {
        $product_warehouse[] = ['product_id' => $product->id, 'warehouse_id' => $warehouse->id, 'variant_id' => $variant->id];
      }
    }

    ProductWarehouse::insert($product_warehouse);
  }


  function addAllProductsToWarehouse(Warehouse $warehouse)
  {
    $products = Product::all(['id', 'has_variants']);

    if (!count($products)) return;

    $products_warehouse = [];

    foreach ($products as $product) {

      $products_warehouse[] = ['product_id' => $product->id, 'warehouse_id' => $warehouse->id, 'variant_id' => null];

      if (!$product->has_variants) continue;

      foreach ($product->variants as $variant) {
        $products_warehouse[] = ['product_id' => $product->id, 'warehouse_id' => $warehouse->id, 'variant_id' => $variant->id];
      }
    }

    ProductWarehouse::insert($products_warehouse);
  }



  function addVariantToWarehouses(ProductVariant $variant)
  {
    $warehouses = Warehouse::all();

    if (!count($warehouses)) return;

    $variant_warehouses = [];

    foreach ($warehouses as $warehouse) {

      $variant_warehouses[] = ['product_id' => $variant->product_id, 'warehouse_id' => $warehouse->id, 'variant_id' => $variant->id];

    }

    ProductWarehouse::insert($variant_warehouses);
  }
}
