<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Arr;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Requests\ProductRequest;
use GuzzleHttp\Handler\Proxy;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
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
    $attr = $req->validate(ProductRequest::ruleOfCreate($req));

    // this is because we set them in $req in ruleOfCreate based on variants or images data
    $attr['has_variants'] = $req->has_variants;
    $attr['has_images'] = $req->has_images;

    $product = Product::create($attr);

    if ($req->has_images) $this->createImages($req, $product);

    if ($req->has_variants) $this->createVariants($req, $product);

    return $this->success([], 'The Product has been created successfully');
  }


  public function update(Request $req)
  {
    $req->merge(['id' => $req->route('id')]);

    $attr = $req->validate(ProductRequest::ruleOfUpdate($req));

    // this is because we set them in $req in ruleOfCreate based on variants or images data
    $attr['has_variants'] = $req->has_variants;
    $attr['has_images'] = $req->has_images;

    $product = Product::find($req->id);

    if (!$product) return $this->error('The product was not found', 404);

    $product->fill($attr);

    $product->save();

    $oldImages = ProductImage::where('product_id', $req->id)->get(['name', 'id']);

    if (count($oldImages)) {

      $imagesNames = Arr::pluck($oldImages, 'name');

      $imagesIds = Arr::pluck($oldImages, 'id');

      $images = array_map(function ($value) {
        return public_path('images/products/') . $value;
      }, $imagesNames);

      File::delete($images);

      ProductImage::destroy($imagesIds);
    }

    $oldVariants = ProductVariant::where('product_id', $req->id)->get(['id', 'product_id']);

    if (count($oldVariants)) {

      $variantsIds = Arr::pluck($oldVariants, 'id');

      ProductVariant::destroy($variantsIds);
    }

    if ($req->has_images) $this->createImages($req, $product);

    if ($req->has_variants) $this->createVariants($req, $product);

    return $this->success(Arr::pluck($oldVariants, 'id'), 'The Product has been updated successfully');
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


  private function createVariants(Request $req, Product $product)
  {
    $variants = [];

    foreach ($req->variants as $variant) {
      $variants[] = ['product_id' => $product->id, 'name' => $variant['name']];
    }

    ProductVariant::insert($variants);
  }
}



    // if ($req->has_images) {

    //   $images = [];

    //   // return $this->success($req->file('images'));

    //   foreach ($req->file('images') as $file) {

    //     $code = $req->code;

    //     $date = date('_y_m_d_s_') . explode('.', explode(' ', microtime())[0])[1];

    //     $ext = '.' . $file['path']->getClientOriginalExtension();

    //     $imageName = 'product_' . $code . $date . $ext;

    //     $file['path']->move(public_path('/images/products'), $imageName);

    //     $images[] = ['product_id' => $product->id, 'name' => $imageName];
    //   }

    //   ProductImage::insert($images);
    // }
