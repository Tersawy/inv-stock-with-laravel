<?php

namespace App\Http\Controllers;

use App\Helpers\CustomException;
use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Models\ProductWarehouse;
use App\Models\ProductVariantImage;
use Illuminate\Support\Facades\File;
use App\Requests\ProductVariantRequest;
use App\Traits\ProductWarehouseOperations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductVariantController extends Controller
{
  use ProductWarehouseOperations;

  public function index(Request $req)
  {
    ProductVariantRequest::validationProductId($req);

    $variants = ProductVariant::where('product_id', $req->productId)->get();

    return $this->success($variants);
  }


  public function show(Request $req)
  {
    ProductVariantRequest::validationId($req);

    $variant = ProductVariant::find($req->id);

    if (!$variant) return $this->error('The variant is not found', 404);

    return $this->success($variant);
  }


  public function create(Request $req)
  {
    try {
      DB::transaction(function () use ($req) {
        $attr = ProductVariantRequest::validationCreate($req);

        $variant = ProductVariant::create($attr);

        if (count($req->images)) $this->createImages($req, $variant);

        $product = Product::select(['id', 'has_variants'])->find($req->productId);

        if (!$product->has_variants) {
          $product->has_variants = true;
          $product->save();
        }

        $this->addVariantToWarehouses($variant);
      }, 10);

      $this->success('The variant has been created successfully', 200);
    } catch (CustomException $e) {
      return $this->error($e->first_error(), $e->status_code());
    }
  }


  public function update(Request $req)
  {
    try {
      DB::transaction(function () use ($req) {
        $attr = ProductVariantRequest::validationUpdate($req);

        $variant = ProductVariant::withCount('images')->find($req->id);

        if (!$variant) return $this->error('The variant is not found', 404);

        if ($variant->images_count > 1) $this->deleteImages($variant->images);

        if (count($req->images)) $this->createImages($req, $variant);

        $variant->name = $attr['name'];

        $variant->save();
      }, 10);

      $this->success('The variant has been updated successfully', 200);
    } catch (CustomException $e) {
      return $this->error($e->first_error(), $e->status_code());
    }
  }


  public function remove(Request $req)
  {
    try {
      DB::transaction(function () use ($req) {

        ProductVariantRequest::validationId($req);

        $variant = ProductVariant::find($req->id);

        if (!$variant) throw CustomException::withMessage('id', 'The variant is not found', 404);

        $products_warehouse = ProductWarehouse::where('product_id', $variant->product_id)->where('variant_id', $variant->id)->get();

        if ($products_warehouse) {
          foreach ($products_warehouse as $product_warehouse) {
            if ($product_warehouse->instock > 0) {
              throw CustomException::withMessage('id', "Sorry, You can't delete variant {$variant->name} because it has {$product_warehouse->instock} quantity", 422);
            }
          }
        }

        if (count($variant->images)) $this->deleteImages($variant->images);

        ProductWarehouse::where('variant_id', $variant->id)->delete();

        $variant->delete();

        $product = Product::select(['id'])->withCount('variants')->find($req->productId);

        if ($product->variants_count < 1) {
          $product->has_variants = false;
          $product->save();
        }
      }, 10);

      $this->success('The variant has been deleted successfully', 200);
    } catch (CustomException $e) {
      return $this->error($e->first_error(), $e->status_code());
    }
  }


  private function createImages(Request $req, ProductVariant $variant)
  {
    $files = Arr::pluck($req->images, 'path');

    $images = [];

    foreach ($files as $file) {
      $imageInfo = explode(';base64,', $file);

      $ext = '.' . str_replace('data:image/', '', $imageInfo[0]);

      $file = str_replace(' ', '+', $imageInfo[1]);

      $date = date('y_m_d_s_') . explode('.', explode(' ', microtime())[0])[1];

      $imageName = 'product_variant_' . $variant->name . $date . $ext;

      Storage::disk('images_products')->put($imageName, base64_decode($file));

      $images[] = ['variant_id' => $variant->id, 'name' => $imageName];
    }

    ProductVariantImage::insert($images);
  }


  private function deleteImages(ProductVariantImage ...$images)
  {
    $imagesNames = Arr::pluck($images, 'name');

    $imagesIds = Arr::pluck($images, 'id');

    $images = array_map(function ($value) {
      return public_path('images/products/') . $value;
    }, $imagesNames);

    File::delete($images);

    ProductVariantImage::destroy($imagesIds);
  }
}
