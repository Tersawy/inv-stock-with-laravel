<?php

namespace App\Traits;

use App\Models\Product;
use Illuminate\Support\Arr;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

trait InvoiceOperations
{
  private function sumVariantsQuantity(&$variants, $detailsHasVariants)
  {
    foreach ($detailsHasVariants as $detail) {

      $variant = $this->getVariantByDetail($variants, $detail);

      $variant['instock'] = $variant['instock'] + (int) $detail['quantity'];
    }
  }


  private function sumProductsQuantity(&$productsHasNoVariants, $detailsHasNoVariants)
  {
    foreach ($detailsHasNoVariants as $detail) {

      $product = $this->getProductById($productsHasNoVariants, $detail['product_id']);

      $product['instock'] = $product['instock'] + (int) $detail['quantity'];
    }
  }


  private function subtractVariantsQuantity(&$variants, $detailsHasVariants)
  {
    foreach ($detailsHasVariants as $detail) {

      $variant = $this->getVariantByDetail($variants, $detail);

      $variant['instock'] = $variant['instock'] - (int) $detail['quantity'];
    }
  }


  private function subtractProductsQuantity(&$productsHasNoVariants, $detailsHasNoVariants)
  {
    foreach ($detailsHasNoVariants as $detail) {

      $product = $this->getProductById($productsHasNoVariants, $detail['product_id']);

      $product['instock'] = $product['instock'] - (int) $detail['quantity'];
    }
  }


  private function updateMultiple($values, $model, string $fieldUpdate, string $primaryKey = 'id', string $caseField = 'id')
  {
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
   * @return array [true] | [false, errorMsg] 
   */
  private function checkProductsWithVariants($details, $products): array
  {
    $result = [true, null];

    foreach ($details as $detail) {

      $product = $this->getProductById($products, $detail['product_id']);

      if (is_null(Arr::get($detail, 'variant_id'))) {
        if ($product->has_variants) return [false, "{$product->name} has variants and you try to use it without variant!"];
        continue;
      }

      if (!$product->has_variants) return [false, "Please check product {$product->name}, because it doesn't have variants"];
    }

    return $result;
  }


  private function checkDistinct($details): array
  {
    $result = [true, null];

    foreach ($details as $index => $detail) {

      foreach ($details as $i => $d) {

        if ($i === $index) continue;

        if (Arr::get($d, 'variant_id') == Arr::get($detail, 'variant_id') && $d['product_id'] == $detail['product_id']) {
          $detailNum = (int) $index + 1;
          $detailDupNum = (int) $i + 1;
          return $result = [false, "The Product.{$detailNum} has been duplicated with Product.{$detailDupNum}"];
        }
      }
    }

    return $result;
  }

  /**
   * Checking the relationship between the products table and the variant table
   * @param array $detailsHasVariants
   * @param \App\Models\ProductVariant[] $variants
   * @param \App\Models\Product[] $products
   * @return array [true] | [false, errorMsg] 
   */
  private function checkingRelations($detailsHasVariants, $variants, $products): array
  {
    $result = [true, null];

    if (count((array) $detailsHasVariants) === count($variants)) return $result;

    foreach ($detailsHasVariants as $detail) {
      $variant = $this->getVariantByDetail($variants, $detail);

      if (is_null($variant)) {
        $product = $this->getProductById($products, $detail['product_id']);
        return $result = [false, "{$product->name} has variants and you try to use it without variant!"];
      }
    }

    return $result;
  }

  /**
   * Checking if product or variants - detail->quantity >= 0
   * because we don't need to have instock with minus
   * This check must be after checking Distinct and Relations
   * @param array $details
   * @param \App\Models\Product[] $products
   * @param \App\Models\ProductVariant[] $variants
   * @return array [true] | [false, errorMsg] 
   */
  private function checkingQuantity($details, $products, $variants): array
  {
    $result = [true, null];

    foreach ($details as $detail) {

      $product = $this->getProductById($products, $detail['product_id']);

      if ($product->has_variants) {

        $variant = $this->getVariantByDetail($variants, $detail);

        $final = $variant['instock'] - $detail['quantity'];

        if ($final < 0) {
          return [false, "{$product->name}-{$variant['name']} has {$variant['instock']} and you try to make return {$detail['quantity']}!"];
        }
      } else {

        $final = $product['instock'] - $detail['quantity'];

        if ($final < 0) {
          return [false, "{$product->name} has {$product['instock']} and you try to make return {$detail['quantity']}!"];
        }
      }
    }

    return $result;
  }

  /**
   * @param array $detailsHasVariants
   * @return ProductVariant[]
   */
  private function getVariants($detailsHasVariants)
  {
    $variants = ProductVariant::query();

    foreach ($detailsHasVariants as $key => $detail) {
      $cond = [['id', $detail['variant_id']], ['product_id', $detail['product_id']]];
      $variants = $key === 0 ? $variants->where($cond) : $variants->orWhere($cond);
    }

    $variants = $variants->get();

    return $variants;
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
   * @param \App\Models\ProductVariant[] $variants
   * @param detail $attr['products'][$index]
   * @return ProductVariant|null
   */
  private function getVariantByDetail($variants, $detailHasVariant): ?ProductVariant
  {
    $result = null;

    foreach ($variants as $variant) {
      if ($variant['product_id'] == $detailHasVariant['product_id'] && $variant['id'] == $detailHasVariant['variant_id']) {
        return $result = $variant;
      }
    }

    return $result;
  }

  /**
   * @param details $attr['products']
   * @param hasVariants bool
   * @return details[] $attr['products']
   */
  private function filterDetailsVariants($details, bool $hasVariants): array
  {
    $result = [];

    foreach ($details as $detail) {
      if ($hasVariants && !is_null(Arr::get($detail, 'variant_id'))) {
        $result[] = $detail;
        continue;
      }
      if (!$hasVariants && is_null(Arr::get($detail, 'variant_id'))) {
        $result[] = $detail;
      }
    }

    return $result;
  }

  /**
   * @param \App\Models\Product[] $products
   * @param hasVariants bool
   * @return Product[]
   */
  private function filterProductsVariants($products, bool $hasVariants): array
  {
    $result = [];

    foreach ($products as $product) {

      if ($hasVariants && $product->has_variants) {
        $result[] = $product;
        continue;
      }

      if (!$hasVariants && !$product->has_variants) {
        $result[] = $product;
      }
    }

    return $result;
  }
}
