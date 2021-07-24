<?php

namespace App\Models;

use App\Traits\ProductDetailsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes, ProductDetailsAttributes;

    protected $fillable = [
        'name',
        'barcode_type',
        'code',
        'price',
        'cost',
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

    public function warehouses()
    {
        return $this->hasMany(ProductWarehouse::class, 'variant_id');
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
