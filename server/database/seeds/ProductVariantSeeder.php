<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('product_variants')->insert([
            ['name' => 'White', 'product_id' => 1],
            ['name' => 'Gray', 'product_id'  => 1],
            ['name' => 'Blue', 'product_id'  => 2],
            ['name' => 'Black', 'product_id' => 2],
        ]);
    }
}
