<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('brands')->insert([
            ['name' => 'Apple', 'image' => 'brand_21_05_30_00_59746000.jpg'],
            ['name' => 'Samsung', 'image' => 'brand_21_05_30_26_56291000.png'],
        ]);
    }
}
