<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('warehouses')->insert([
            [
                'name'      => 'Giza warhouse',
                'phone'     => '0123456789',
                'email'     => 'giza@gmail.com',
                'country'   => 'Dokki',
                'city'      => 'dokki',
                'zip_code'  => '1234'
            ],
            [
                'name'      => 'Cairo warhouse',
                'phone'     => '45720345',
                'email'     => 'cairo@gmail.com',
                'country'   => 'Maadi',
                'city'      => 'Maadi',
                'zip_code'  => '45678'
            ],
            [
                'name'      => 'Alex warhouse',
                'phone'     => '023158559',
                'email'     => 'alex@gmail.com',
                'country'   => 'Sidy Beshr',
                'city'      => 'Sidy Beshr',
                'zip_code'  => '87595'
            ],
        ]);
    }
}
