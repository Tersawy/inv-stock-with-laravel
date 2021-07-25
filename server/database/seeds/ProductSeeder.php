<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products')->insert([
            [
                'name'              => 'Iphone 6 plus',
                'barcode_type'      => 'CODE128',
                'code'              => '12345678',
                'price'             => 2700,
                'cost'              => 2400,
                'has_variants'      => 1,
                'category_id'       => 1,
                'main_unit_id'      => 1,
                'purchase_unit_id'  => 1,
                'sale_unit_id'      => 1,
            ],
            [
                'name'              => 'Samsung A10',
                'barcode_type'      => 'CODE128',
                'code'              => '45678150',
                'price'             => 7700,
                'cost'              => 7000,
                'has_variants'      => 1,
                'category_id'       => 1,
                'main_unit_id'      => 1,
                'purchase_unit_id'  => 1,
                'sale_unit_id'      => 2,
            ],
            [
                'name'              => 'Banana',
                'barcode_type'      => 'CODE128',
                'code'              => '32165809',
                'price'             => 7700,
                'cost'              => 7000,
                'has_variants'      => 0,
                'category_id'       => 1,
                'main_unit_id'      => 2,
                'purchase_unit_id'  => 3,
                'sale_unit_id'      => 4,
            ],
        ]);
    }
}
