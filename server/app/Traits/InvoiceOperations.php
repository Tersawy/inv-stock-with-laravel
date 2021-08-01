<?php

namespace App\Traits;

use App\Helpers\CustomException;
use App\Models\Product;
use Illuminate\Support\Arr;
use App\Models\ProductWarehouse;
use Illuminate\Support\Facades\DB;

trait InvoiceOperations
{
  private function get_products_warehouse_by_details($warehouseId, $details)
  {
    $query = ProductWarehouse::query();

    foreach ($details as $key => $detail) {
      $cond = [
        ['warehouse_id', $warehouseId],
        ['product_id', $detail['product_id']],
        ['variant_id', Arr::get($detail, 'variant_id')]
      ];

      $query = $key === 0 ? $query->where($cond) : $query->orWhere($cond);
    }

    $products_warehouse = $query->get();

    return $products_warehouse;
  }


  private function sum_instock($details, &$products_warehouse, $products)
  {
    foreach ($details as $detail) {

      $product = $this->getProductById($products, $detail['product_id']);

      $unit = $product->{$this->unitName};

      $quantity = $unit->operator == '/' ? $detail['quantity'] / $unit->value : $detail['quantity'] * $unit->value;

      $product_warehouse = $this->getProductWarehouseByDetail($products_warehouse, $detail);

      $product_warehouse->instock += $quantity;
    }
  }


  private function subtract_instock($details, &$products_warehouse, $products)
  {
    foreach ($details as $detail) {

      $product = $this->getProductById($products, $detail['product_id']);

      $unit = $product->{$this->unitName};

      $quantity = $unit->operator == '/' ? $detail['quantity'] / $unit->value : $detail['quantity'] * $unit->value;

      $product_warehouse = $this->getProductWarehouseByDetail($products_warehouse, $detail);

      $product_warehouse->instock -= $quantity;
    }
  }


  private function update_instock($products_warehouse)
  {
    $this->updateMultiple($products_warehouse, ProductWarehouse::class, 'instock');
  }


  private function updateMultiple($values, $model, string $fieldUpdate, string $primaryKey = 'id', string $caseField = 'id')
  {
    if (count($values) < 1) return;

    $table = $model::getModel()->getTable();

    $cases = [];
    $ids = [];
    $params = [];

    foreach ($values as $value) {
      $id = (int) $value[$primaryKey];
      $cases[] = "WHEN {$value[$caseField]} then ?";
      $params[] = $value[$fieldUpdate];
      $ids[] = $id;
    }

    $ids = implode(',', $ids);
    $cases = implode(' ', $cases);

    DB::update("UPDATE `{$table}` SET `$fieldUpdate` = CASE `$caseField` {$cases} END WHERE `$caseField` in ({$ids})", $params);
  }


  /**
   * @param details $attr['products']
   * @param \App\Models\Product[] $products
   * @return void
   */
  private function check_products_with_variants($details, $products)
  {
    foreach ($details as $i => $detail) {

      $product = $this->getProductById($products, $detail['product_id']);

      $num = $i + 1;

      if (is_null(Arr::get($detail, 'variant_id'))) {
        if ($product->has_variants) throw CustomException::withMessage("detail.{$num}", "{$product->name} has variants and you try to use it without variant!");
        continue;
      }

      if (!$product->has_variants) throw CustomException::withMessage("detail.{$num}", "Please check product {$product->name}, because it doesn't have variants");
    }
  }


  private function check_distinct($details)
  {
    foreach ($details as $index => $detail) {

      foreach ($details as $i => $d) {

        if ($i === $index) continue;

        if (Arr::get($d, 'variant_id') == Arr::get($detail, 'variant_id') && $d['product_id'] == $detail['product_id']) {
          $detailNum = (int) $index + 1;
          $detailDupNum = (int) $i + 1;
          throw CustomException::withMessage("detail.{$detailNum}", "The Product.{$detailNum} has been duplicated with Product.{$detailDupNum}");
        }
      }
    }
  }

  /**
   * Checking the relationship between the products table and the variant table
   * @param array $details
   * @param \App\Models\ProductWarehouse[] $products_warehouse
   * @param \App\Models\Product[] $products
   * @return void
   */
  private function checking_relations($details, $products_warehouse, $products)
  {
    foreach ($details as $i => $detail) {

      $product_warehouse = $this->getProductWarehouseByDetail($products_warehouse, $detail);

      if (is_null($product_warehouse)) {
        $product = $this->getProductById($products, $detail['product_id']);
        $num = $i + 1;
        throw CustomException::withMessage("detail.{$num}", "{$product->name} has variants and you try to use it without variant!");
      }
    }
  }

  /**
   * Checking if products_warehouse - detail->quantity >= 0
   * because we don't need to have instock with minus
   * This check must be after checking Distinct and Relations
   * @param array $details
   * @param \App\Models\ProductWarehouse[] $products_warehouse
   * @param \App\Models\Product[] $products
   * @return void
   */
  private function checking_quantity($details, $products_warehouse, $products)
  {
    foreach ($details as $i => $detail) {

      $product = $this->getProductById($products, $detail['product_id']);

      $product_warehouse = $this->getProductWarehouseByDetail($products_warehouse, $detail);

      $num = $i + 1;

      $final = $product_warehouse->instock - $detail['quantity'];

      if ($final < 0) {
        throw CustomException::withMessage("detail.{$num}", "{$product->name} has {$product_warehouse->instock} quantity in stock and you try to subtract {$detail['quantity']} quantity!");
      }
    }
  }

  /**
   * @param \App\Models\Product[] $products
   * @param int $id
   * @return Product|null 
   */
  private function getProductById($products, int $id): ?Product
  {
    $result = null;

    foreach ($products as $product) {
      if ($product->id == $id) return $product;
    }

    return $result;
  }

  /**
   * @param \App\Models\ProductWarehouse[] $products_warehouse
   * @param detail $attr['products'][$index]
   * @return ProductWarehouse|null
   */
  private function getProductWarehouseByDetail($products_warehouse, $detail): ?ProductWarehouse
  {
    $result = null;

    foreach ($products_warehouse as $product_warehouse) {
      if ($product_warehouse->product_id == $detail['product_id'] && $product_warehouse->variant_id == Arr::get($detail, 'variant_id')) {
        return $result = $product_warehouse;
      }
    }

    return $result;
  }
}
