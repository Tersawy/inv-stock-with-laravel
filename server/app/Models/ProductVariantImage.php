<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantImage extends Model
{
    protected $fillable = ['name', 'variant_id'];

    protected $appends = ['path'];

    public function getPathAttribute()
    {
        $type = pathinfo($this->name, PATHINFO_EXTENSION);

        $imagePath = public_path('/images/products/') . $this->name;

        if (file_exists($imagePath)) {
            $data = file_get_contents($imagePath);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            return $base64;
        }
    }
}
