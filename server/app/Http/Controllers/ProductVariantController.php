<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Models\ProductWarehouse;
use App\Models\ProductVariantImage;
use Illuminate\Support\Facades\File;
use App\Requests\ProductVariantRequest;
use Illuminate\Support\Facades\Storage;

class ProductVariantController extends Controller
{
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

    if(!$variant) return $this->error('The variant is not found', 404);

    return $this->success($variant);
  }


  public function create(Request $req)
  {
    $attr = ProductVariantRequest::validationCreate($req);

    $variant = ProductVariant::create($attr);

    if (count($req->images)) $this->createImages($req, $variant);

    $product = Product::select(['id', 'has_variants'])->find($req->productId);

    if (!$product->has_variants) {
      $product->has_variants = true;
      $product->save();
    }

    $this->success('The variant has been created successfully', 200);
  }


  public function update(Request $req)
  {
    $attr = ProductVariantRequest::validationUpdate($req);

    $variant = ProductVariant::withCount('images')->find($req->id);

    if(!$variant) return $this->error('The variant is not found', 404);

    if ($variant->images_count > 1) $this->deleteImages($variant->images);

    if (count($req->images)) $this->createImages($req, $variant);

    $variant->name = $attr['name'];

    $variant->save();

    $this->success('The variant has been updated successfully', 200);
  }


  public function remove(Request $req)
  {
    ProductVariantRequest::validationId($req);

    $variant = ProductVariant::find($req->id);

    if(!$variant) return $this->error('The variant is not found', 404);
    
    if ($variant->instock > 0) {
      return $this->error("Sorry, You can't delete variant {$variant->name} because it has {$variant->instock} quantity", 422);
    }

    if (count($variant->images)) $this->deleteImages($variant->images);

    ProductWarehouse::where('variant_id', $variant->id)->delete();

    $variant->delete();

    $product = Product::select(['id'])->withCount('variants')->find($req->productId);

    if ($product->variants_count < 1) {
      $product->has_variants = false;
      $product->save();
    }

    $this->success('The variant has been deleted successfully', 200);
  }


  private function createImages(Request $req, ProductVariant $variant)
  {
    $files = Arr::pluck($req->images, 'path');

    $images = [];

    foreach ($files as $file)
    {
      $imageInfo = explode(';base64,', $file);

      $ext = '.' . str_replace('data:image/', '', $imageInfo[0]);

      $file = str_replace(' ', '+', $imageInfo[1]);

      $date = date('y_m_d_s_') . explode('.', explode(' ', microtime())[0])[1];

      $imageName = 'product_variant_' . $variant->name . $date . $ext;

      Storage::disk('images_products')->put($imageName, base64_decode($file));

      $images[] = ['product_variant_id' => $variant->id, 'name' => $imageName];
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
