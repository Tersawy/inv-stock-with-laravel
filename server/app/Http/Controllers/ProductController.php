<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Arr;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Models\ProductWarehouse;
use App\Requests\ProductRequest;
use App\Traits\ProductWarehouseOperations;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
  use ProductWarehouseOperations;

  public function index()
  {
    $category = ['category' => function ($query) {
      $query->select(['id', 'name']);
    }];

    $brand = ['brand' => function ($query) {
      $query->select(['id', 'name']);
    }];

    $unit = ['unit' => function ($query) {
      $query->select(['id', 'short_name']);
    }];

    $images = ['images' => function ($query) {
      $query->select(['name', 'product_id']);
    }];

    $withFields = array_merge([], $category, $brand, $unit, $images);

    $products = Product::with($withFields)->get(['id', 'name', 'code', 'price', 'instock', 'main_unit_id', 'brand_id', 'category_id']);

    $products = $products->map(function ($product) {
      return [
        'id'        => $product->id,
        'name'      => $product->name,
        'code'      => $product->code,
        'price'     => $product->price,
        'quantity'  => $product->instock,
        'unit'      => $product->unit->short_name,
        'brand'     => $product->brand ? $product->brand->name : 'N/D',
        'category'  => $product->category->name,
        'image'     => count($product->images) ? $product->images[0]['name'] : 'product_empty.jpeg'
      ];
    });

    return $this->success($products);
  }


  public function options()
  {
    $variants = ['variants' => function ($query) {
      $query->select(['id', 'name', 'product_id']);
    }];

    $images = ['images' => function ($query) {
      $query->select(['name', 'product_id']);
    }];

    $withFields = array_merge([], $variants, $images);

    $products = Product::with($withFields)->get(['id', 'name', 'code', 'has_variants']);

    $newProducts = [];

    foreach ($products as $product) {
      $newProduct = [
        'id'    => $product->id,
        'name'  => $product->name,
        'code'  => $product->code,
        'image' => count($product->images) ? $product->images[0]['name'] : 'product_empty.jpeg',
      ];

      if (!$product->has_variants) {
        $newProducts[] = $newProduct;
      } else {
        foreach ($product->variants as $variant) {
          $newProduct['variant'] = $variant->name;
          $newProduct['variant_id'] = $variant->id;
          $newProducts[] = $newProduct;
        }
      }
    }

    // return $this->success($products);
    return $this->success($newProducts);
  }


  public function details(Request $req)
  {
    $req->merge(['id' => $req->route('id')]);

    $req->validate(['id' => ['required', 'numeric', 'min:1']]);

    $saleUnit = ['sale_unit' => function ($query) {
      $query->select(['id', 'short_name', 'value', 'operator']);
    }];

    $purchaseUnit = ['purchase_unit' => function ($query) {
      $query->select(['id', 'short_name', 'value', 'operator']);
    }];

    $withFields = array_merge([], $saleUnit, $purchaseUnit);

    $columns = ['id', 'purchase_unit_id', 'sale_unit_id', 'cost', 'price', 'tax', 'tax_method', 'instock'];

    $product = Product::with($withFields)->find($req->id, $columns);

    return $this->success($product->details());
  }


  public function show(Request $req)
  {
    $req->merge(['id' => $req->route('id')]);

    $req->validate(['id' => ['required', 'numeric', 'min:1']]);

    $images = ['images' => function ($query) {
      $query->select(['name', 'product_id']);
    }];

    $variants = ['variants' => function ($query) {
      $query->select(['name', 'product_id']);
    }];

    $withFields = array_merge([], $images, $variants);

    $product = Product::where('id', $req->id)->with($withFields)->get()->first();

    return $this->success($product);
  }


  public function create(Request $req)
  {
    $attr = ProductRequest::validationCreate($req);

    $product = Product::create($attr);

    if ($req->has_images) $this->createImages($req, $product);

    if ($req->has_variants) $this->createVariants($req, $product);

    $this->addProductToWarehouses($product);

    return $this->success([], 'The product has been created successfully');
  }


  public function update(Request $req)
  {
    $attr = ProductRequest::validationUpdate($req);

    $product = Product::find($req->id);

    if (!$product) return $this->error('The product was not found', 404);

    $product->fill($attr);

    $product->save();

    if ($product->has_images) $this->deleteImages($product->images);

    if ($req->has_images) $this->createImages($req, $product);

    return $this->success($product, 'The product has been updated successfully');
  }


  public function moveToTrash(Request $req)
  {
    ProductRequest::validationId($req);

    $product = Product::find($req->id);

    if (!$product) return $this->error('The product was not found', 404);

    if ($product->has_variants) {

      $variants = $product->variants;

      foreach ($variants as $variant) {
        if ($variant->instock > 0) {
          return $this->error("Sorry, You can\'t remove this product because it has a variant ({$variant->name}) has instock", 422);
        }
      }
    } else {
      if ($product->instock > 0) {
        return $this->error('Sorry, You can\'t remove this product because it has instock', 422);
      }
    }

    ProductWarehouse::where('product_id', $product->id)->update(['deleted_at', now()]);

    $product->delete();

    return $this->success('The product has been moved to trash successfully');
  }


  public function trashed()
  {
    $products = Product::onlyTrashed()->get();

    return $this->success($products);
  }


  public function restore(Request $req)
  {
    ProductRequest::validationId($req);

    $isDone = Product::onlyTrashed()->where('id', $req->id)->restore();

    if (!$isDone) return $this->error('The product is not in the trash', 404);

    ProductWarehouse::where('product_id', $req->id)->update(['deleted_at', null]);

    return $this->success($req->id, 'The product has been restored successfully');
  }


  public function remove(Request $req)
  {
    ProductRequest::validationId($req);

    $isDone = Product::onlyTrashed()->where('id', $req->id)->forceDelete();

    if (!$isDone) return $this->error('The product is not in the trash', 404);

    return $this->success($req->id, 'The product has been deleted successfully');
  }


  private function createImages(Request $req, Product $product)
  {
    $files = Arr::pluck($req->images, 'path');

    foreach ($files as $file) {

      $imageInfo = explode(';base64,', $file);

      $ext = '.' . str_replace('data:image/', '', $imageInfo[0]);

      $file = str_replace(' ', '+', $imageInfo[1]);

      $code = $req->code;

      $date = date('_y_m_d_s_') . explode('.', explode(' ', microtime())[0])[1];

      $imageName = 'product_' . $code . $date . $ext;

      Storage::disk('images_products')->put($imageName, base64_decode($file));

      $images[] = ['product_id' => $product->id, 'name' => $imageName];
    }

    ProductImage::insert($images);
  }


  private function deleteImages(ProductImage ...$images)
  {
    $imagesNames = Arr::pluck($images, 'name');

    $imagesIds = Arr::pluck($images, 'id');

    $images = array_map(function ($value) {
      return public_path('images/products/') . $value;
    }, $imagesNames);

    File::delete($images);

    ProductImage::destroy($imagesIds);
  }
}
