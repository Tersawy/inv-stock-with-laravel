<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Arr;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Helpers\CustomException;
use App\Models\ProductWarehouse;
use App\Requests\ProductRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Traits\ProductWarehouseOperations;

class ProductController extends Controller
{
  use ProductWarehouseOperations;

  protected $filterationFields = [
    'name'      => 'name',
    'category'  => 'category_id',
    'brand'     => 'brand_id',
    'code'      => 'code'
  ];

  protected $searchFields = ['name', 'code'];

  public function index(Request $req)
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
      $query->select(['name', 'product_id', 'default'])->where('default', 1);
    }];

    $with_fields = array_merge($category, $brand, $unit, $images);

    $query = Product::query();

    $this->handleQuery($req, $query);

    $products = $query->with($with_fields)->select(['id', 'name', 'code', 'price', 'unit_id', 'brand_id', 'category_id'])->paginate($req->per_page);

    $products->getCollection()->transform(function ($product) {
      return [
        'id'        => $product->id,
        'name'      => $product->name,
        'code'      => $product->code,
        'price'     => $product->price,
        'instock'   => $product->instock,
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

    $with_fields = array_merge($variants, $images);

    $products = Product::with($with_fields)->get(['id', 'name', 'code', 'has_variants']);

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

    return $this->success($newProducts);
  }


  public function details(Request $req)
  {
    $req->validate([
      'id'            => ['required', 'numeric', 'min:1'],
      'warehouse_id'  => ['required', 'numeric', 'min:1'],
      'variant_id'    => ['nullable', 'numeric', 'min:1'],
    ]);

    $saleUnit = ['sale_unit' => function ($query) {
      $query->select(['id', 'short_name', 'value', 'operator']);
    }];

    $purchaseUnit = ['purchase_unit' => function ($query) {
      $query->select(['id', 'short_name', 'value', 'operator']);
    }];

    $purchaseUnit = ['purchase_unit' => function ($query) {
      $query->select(['id', 'short_name', 'value', 'operator']);
    }];

    $products_warehouse = ['warehouse' => function ($query) use ($req) {
      $query->where('warehouse_id', $req->warehouse_id)->where('variant_id', $req->variant_id);
    }];

    $with_fields = array_merge($saleUnit, $purchaseUnit, $products_warehouse);

    $columns = ['id', 'purchase_unit_id', 'sale_unit_id', 'cost', 'price', 'tax', 'tax_method'];

    $product = Product::with($with_fields)->find($req->id, $columns);

    return $this->success($product->details());
  }


  public function show(Request $req)
  {
    ProductRequest::validationId($req);

    $images = ['images' => function ($query) {
      $query->select(['name', 'product_id', 'default']);
    }];

    $product = Product::where('id', $req->id)->with($images)->get()->first();

    if (!$product) return $this->error('Product is not found !', 404);

    $product->images = $product->images->map(function ($productImage) {
      $productImage->path = $productImage->path;
      return $productImage;
    });

    return $this->success($product);
  }


  public function create(Request $req)
  {
    try {
      DB::transaction(function () use ($req) {
        $attr = ProductRequest::validationCreate($req);

        $product = Product::create($attr);

        if ($req->has_images) $this->createImages($req, $product);

        if ($req->has_variants) $this->createVariants($req, $product);

        $this->addProductToWarehouses($product);
      }, 10);

      return $this->success([], 'The product has been created successfully');
    } catch (CustomException $e) {
      return $this->error($e->first_error(), $e->status_code());
    }
  }


  public function update(Request $req)
  {
    try {
      DB::transaction(function () use ($req) {
        $attr = ProductRequest::validationUpdate($req);

        $product = Product::find($req->id);

        if (!$product) return $this->error('The product was not found', 404);

        $product->fill($attr);

        $product->save();

        if ($product->has_images) $this->deleteImages($product->images);

        if ($req->has_images) $this->createImages($req, $product);
      }, 10);

      return $this->success($req->id, 'The product has been updated successfully');
    } catch (CustomException $e) {
      return $this->error($e->first_error(), $e->status_code());
    }
  }


  public function remove(Request $req)
  {
    try {
      DB::transaction(function () use ($req) {
        ProductRequest::validationId($req);

        $product = Product::find($req->id);

        if (!$product) throw CustomException::withMessage('id', 'The product was not found', 404);

        foreach ($product->warehouse as $product_warehouse) {

          if ($product->has_variants && !is_null($product_warehouse->variant_id) && $product_warehouse->instock > 0) {
            throw CustomException::withMessage('id', "The product has quantity in variant {$product_warehouse->variant->name} in warehouse {$product_warehouse->warehouse->name}");
          }

          if (!$product->has_variants && is_null($product_warehouse->variant_id) && $product_warehouse->instock > 0) {
            throw CustomException::withMessage('id', "The product {$product->name} has quantity in warehouse {$product_warehouse->warehouse->name}");
          }
        }

        ProductWarehouse::where('product_id', $product->id)->update(['deleted_at' => now()]);

        $product->delete();
      }, 10);

      return $this->success('The product has been deleted successfully');
    } catch (CustomException $e) {
      return $this->error($e->first_error(), $e->status_code());
    }
  }


  protected function createImages(Request $req, Product $product)
  {
    $files = Arr::pluck($req->images, 'path');

    foreach ($files as $key => $file) {

      $imageInfo = explode(';base64,', $file);

      $ext = '.' . str_replace('data:image/', '', $imageInfo[0]);

      $file = str_replace(' ', '+', $imageInfo[1]);

      $code = $req->code;

      $date = date('_y_m_d_s_') . explode('.', explode(' ', microtime())[0])[1];

      $imageName = 'product_' . $code . $date . $ext;

      Storage::disk('images_products')->put($imageName, base64_decode($file));

      $images[] = ['product_id' => $product->id, 'name' => $imageName, 'default' => $req->images[$key]['default']];
    }

    ProductImage::insert($images);
  }


  protected function deleteImages($images)
  {
    $imagesNames = Arr::pluck($images, 'name');

    $imagesIds = Arr::pluck($images, 'id');

    $images = array_map(function ($value) {
      return public_path('images/products/') . $value;
    }, $imagesNames);

    File::delete($images);

    ProductImage::destroy($imagesIds);
  }


  protected function createVariants(Request $req, Product $product)
  {
    $variants = [];

    foreach ($req->variants as $variant) {
      $variants[] = ['product_id' => $product->id, 'name' => $variant['name']];
    }

    ProductVariant::insert($variants);
  }
}
