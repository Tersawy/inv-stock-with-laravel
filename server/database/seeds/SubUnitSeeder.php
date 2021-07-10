<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sub_units')->insert([
            ['name' => 'Dezon Box', 'short_name' => 'Box',  'value' => 1,       'main_unit_id' => 1],
            ['name' => 'piece',     'short_name' => 'pc',   'value' => 12,      'main_unit_id' => 1],
            ['name' => 'ton',       'short_name' => 'ton',  'value' => 1,       'main_unit_id' => 2],
            ['name' => 'Kilogram',  'short_name' => 'Kg',   'value' => 1000,    'main_unit_id' => 2],
            ['name' => 'Gram',      'short_name' => 'g',    'value' => 1000000, 'main_unit_id' => 2],
        ]);
    }
}
