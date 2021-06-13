<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    const TAX_EXCLUSIVE = 0;
    const TAX_INCLUSIVE = 1;
    const TAX_METHODS   = [Product::TAX_EXCLUSIVE, Product::TAX_INCLUSIVE];

    const DISCOUNT_FIXED = 0;
    const DISCOUNT_PERCENT = 1;
    const DISCOUNT_METHODS   = [Product::DISCOUNT_FIXED, Product::DISCOUNT_PERCENT];

    protected $fillable = [
        'name',
        'barcode_type',
        'code',
        'price',
        'cost',
        'instock',
        'minimum',
        'tax',
        'tax_method',
        'note',
        'category_id',
        'brand_id',
        'main_unit_id',
        'purchase_unit_id',
        'sale_unit_id',
        'has_variants',
        'has_images'
    ];

    protected $casts = [
        'has_images'    => 'boolean',
        'has_variants'  => 'boolean',
    ];

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