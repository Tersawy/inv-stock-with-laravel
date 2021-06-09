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
            ['name' => 'piece', 'short_name' => 'pc'],
            ['name' => 'ton', 'short_name' => 'ton'],
        ]);
    }
}
