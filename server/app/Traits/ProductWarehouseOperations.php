<?php

namespace App\Traits;

use App\Models\Product;
use App\Models\Warehouse;
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

      $product_warehouse[] = ['product_id' => $product->id, 'warehouse_id' => $warehouse->id, 'product_variant_id' => null];

      if (!count($variants)) continue;

      foreach ($variants as $variant) {
        $product_warehouse[] = ['product_id' => $product->id, 'warehouse_id' => $warehouse->id, 'product_variant_id' => $variant->id];
      }
    }

    ProductWarehouse::insert($product_warehouse);
  }


  function addAllProductsToWarehouse(Warehouse $warehouse)
  {
    $products = Product::all();

    if (!count($products)) return;

    $products_warehouse = [];

    foreach ($products as $product) {

      $products_warehouse[] = ['product_id' => $product->id, 'warehouse_id' => $warehouse->id, 'product_variant_id' => null];

      if (!$product->has_variants) continue;

      foreach ($product->variants as $variant) {
        $products_warehouse[] = ['product_id' => $product->id, 'warehouse_id' => $warehouse->id, 'product_variant_id' => $variant->id];
      }
    }

    ProductWarehouse::insert($products_warehouse);
  }


  function sumWarehouseQuantity($warehouseId, $details, $products, $unitName)
  {
    foreach ($details as $detail) {

      $product_warehouse = ProductWarehouse::where('warehouse_id', $warehouseId)->where('product_id', $detail['product_id']);

      if (!is_null($detail['variant_id'])) {
        $product_warehouse = $product_warehouse->where('product_variant_id', $detail['variant_id']);
      }

      $product_warehouse = $product_warehouse->first();

      $product = $this->getProductById($products, $detail['product_id']);

      $unit = $product->{$unitName};

      $quantity = $unit->operator == '/' ? $detail['quantity'] / $unit->value : $detail['quantity'] * $unit->value;

      $product_warehouse->instock += $quantity;

      $product_warehouse->save();
    }
  }


  function subtractWarehouseQuantity($warehouseId, $details, $products, $unitName)
  {
    foreach ($details as $detail) {

      $product_warehouse = ProductWarehouse::where('warehouse_id', $warehouseId)->where('product_id', $detail['product_id']);

      if (!is_null($detail['variant_id'])) {
        $product_warehouse = $product_warehouse->where('product_variant_id', $detail['variant_id']);
      }

      $product_warehouse = $product_warehouse->first();

      $product = $this->getProductById($products, $detail['product_id']);

      $unit = $product->{$unitName};

      $quantity = $unit->operator == '/' ? $detail['quantity'] / $unit->value : $detail['quantity'] * $unit->value;

      $product_warehouse->instock -= $quantity;

      $product_warehouse->save();
    }
  }
}
