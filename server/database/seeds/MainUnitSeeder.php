<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MainUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('main_units')->insert([
            ['name' => 'Dezon Box', 'short_name' => 'Box'],
            ['name' => 'ton', 'short_name' => 'ton'],
        ]);
    }
}
