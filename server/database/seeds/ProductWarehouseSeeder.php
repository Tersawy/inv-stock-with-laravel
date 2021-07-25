<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductWarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('product_warehouses')->insert([
            [ 'product_id' => 1, 'variant_id' => null, 'warehouse_id' => 1 ],
            [ 'product_id' => 1, 'variant_id' => null, 'warehouse_id' => 2 ],
            [ 'product_id' => 1, 'variant_id' => null, 'warehouse_id' => 3 ],
            [ 'product_id' => 2, 'variant_id' => null, 'warehouse_id' => 1 ],
            [ 'product_id' => 2, 'variant_id' => null, 'warehouse_id' => 2 ],
            [ 'product_id' => 2, 'variant_id' => null, 'warehouse_id' => 3 ],
            [ 'product_id' => 3, 'variant_id' => null, 'warehouse_id' => 1 ],
            [ 'product_id' => 3, 'variant_id' => null, 'warehouse_id' => 2 ],
            [ 'product_id' => 3, 'variant_id' => null, 'warehouse_id' => 3 ],
            [ 'product_id' => 1, 'variant_id' => 1, 'warehouse_id' => 1 ],
            [ 'product_id' => 1, 'variant_id' => 1, 'warehouse_id' => 2 ],
            [ 'product_id' => 1, 'variant_id' => 1, 'warehouse_id' => 3 ],
            [ 'product_id' => 1, 'variant_id' => 2, 'warehouse_id' => 1 ],
            [ 'product_id' => 1, 'variant_id' => 2, 'warehouse_id' => 2 ],
            [ 'product_id' => 1, 'variant_id' => 2, 'warehouse_id' => 3 ],
            [ 'product_id' => 2, 'variant_id' => 3, 'warehouse_id' => 1 ],
            [ 'product_id' => 2, 'variant_id' => 3, 'warehouse_id' => 2 ],
            [ 'product_id' => 2, 'variant_id' => 3, 'warehouse_id' => 3 ],
            [ 'product_id' => 2, 'variant_id' => 4, 'warehouse_id' => 1 ],
            [ 'product_id' => 2, 'variant_id' => 4, 'warehouse_id' => 2 ],
            [ 'product_id' => 2, 'variant_id' => 4, 'warehouse_id' => 3 ],
        ]);
    }
}
