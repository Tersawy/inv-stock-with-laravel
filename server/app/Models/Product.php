<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'barcode_type',
        'code',
        'price',
        'cost',
        'instock',
        'minimum',
        'tax',
        'note',
        'category_id',
        'brand_id',
        'main_unit_id',
        'purchase_unit_id',
        'sale_unit_id',
        'has_variants',
        'has_images'
    ];

    const TAX_EXCLUSIVE = 0;
    const TAX_INCLUSIVE = 1;
    const TAX_METHODS   = [Product::TAX_EXCLUSIVE, Product::TAX_INCLUSIVE];

    private static function rules(Request $req)
    {
        $rules = [
            'barcode_type'      => ['required', 'string', 'max:54', 'min:3'],
            'cost'              => ['required', 'numeric', 'min:1'],
            'price'             => ['required', 'numeric', 'min:1', 'gte:cost'],
            'minimum'           => ['required', 'numeric', 'min:0'],
            'tax'               => ['required', 'numeric', 'min:0'],
            'note'              => ['string', 'max:255', 'nullable'],
            'category_id'       => ['required', 'numeric', 'min:1', 'exists:categories,id'],
            'brand_id'          => ['exclude_if:brand_id,0', 'required', 'numeric', 'min:1', 'exists:brands,id'],
            'main_unit_id'      => ['required', 'numeric', 'min:1', 'exists:main_units,id'],
            'purchase_unit_id'  => ['required', 'numeric', 'min:1', 'exists:sub_units,id'],
            'sale_unit_id'      => ['required', 'numeric', 'min:1', 'exists:sub_units,id'],
            'has_variants'      => ['required', 'boolean'],
            'has_images'        => ['required', 'boolean'],
        ];

        $variants_rules = [
            'variants'              => ['required', 'array', 'max:54', 'min:1'],
            'variants.*'            => ['required', 'array', 'max:54', 'min:1'],
            'variants.*.name'       => ['required', 'string', 'distinct', 'max:54', 'min:3']
        ];

        if ($req->has_variants && is_array($req->variants) && count($req->variants)) {
            $rules = array_merge($rules, $variants_rules);
        } else {
            $req->has_variants = false;
        }

        $images_rules = [
            'images'            => ['required', 'array', 'max:54', 'min:1'],
            'images.*'          => ['required', 'array', 'max:54', 'min:1'],
            'images.*.path'     => ['required', 'base64_image:jpeg,png,jpg']
        ];

        if ($req->has_images) {
            $rules = array_merge($rules, $images_rules);
        } else {
            $req->has_images = false;
        }

        return $rules;
    }


    protected static function ruleOfCreate(Request $req)
    {
        $rules = Product::rules($req);

        $newRules = [
            'name' => ['required', 'string', 'max:255', 'min:3', 'unique:products'],
            'code' => ['required', 'string', 'max:54', 'min:3', 'unique:products']
        ];

        return array_merge($rules, $newRules);
    }


    protected static function ruleOfUpdate(Request $req)
    {
        $rules = Product::rules($req);

        $newRules = [
            'id'    => ['required', 'numeric', 'min:1'],
            'name'  => ['required', 'string', 'max:255', 'min:3', 'unique:products,name,' . $req->id],
            'code'  => ['required', 'string', 'max:54', 'min:3', 'unique:products,code,' . $req->id]
        ];

        return array_merge($rules, $newRules);
    }


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function unit()
    {
        return $this->belongsTo(MainUnit::class, 'main_unit_id');
    }

    public function purchase_unit()
    {
        return $this->belongsTo(SubUnit::class);
    }

    public function sale_unit()
    {
        return $this->belongsTo(SubUnit::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    // const BARCODE_UPC_TYPE       = "UPC";
    // const BARCODE_EAN8_TYPE      = "EAN-8";
    // const BARCODE_EAN13_TYPE     = "EAN-13";
    // const BARCODE_CODE39_TYPE    = "CODE39";
    // const BARCODE_CODE128_TYPE   = "CODE128";

    // const BARCODE_UPC_LENGTH       = 11;
    // const BARCODE_EAN8_LENGTH      = 7;
    // const BARCODE_EAN13_LENGTH     = 12;
    // const BARCODE_CODE39_LENGTH    = 8;
    // const BARCODE_CODE128_LENGTH   = 8;

    // const BARCODE_TYPES = [
    //     static::BARCODE_UPC_TYPE,
    //     static::BARCODE_EAN8_TYPE,
    //     static::BARCODE_EAN13_TYPE,
    //     static::BARCODE_CODE39_TYPE,
    //     static::BARCODE_CODE128_TYPE
    // ];
}
