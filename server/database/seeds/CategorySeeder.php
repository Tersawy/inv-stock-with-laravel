<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert([
            ['name' => 'Mobiles', 'code' => 'A13'],
            ['name' => 'Clothes', 'code' => 'A14'],
        ]);
    }
}
