<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('units')->insert([
            ['name' => 'Ton',       'short_name' => 'ton',  'operator' => '*', 'value' => 1,    'main_unit_id' => null],
            ['name' => 'Kilogram',  'short_name' => 'kg',   'operator' => '/', 'value' => 1000, 'main_unit_id' => 1],
            ['name' => 'Piece',     'short_name' => 'pc',   'operator' => '*', 'value' => 1,    'main_unit_id' => null],
            ['name' => 'Dezon Box', 'short_name' => 'box',  'operator' => '*', 'value' => 12,   'main_unit_id' => 3],
        ]);
    }
}
