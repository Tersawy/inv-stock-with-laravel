<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('suppliers')->insert([
            [
                'name'      => 'IT Supply',
                'email'     => 'Supplier@example.com',
                'phone'     => '360-723-2330',
                'country'   => 'USA',
                'city'      => 'Florida',
                'address'   => '3398 Pinnickinick Street Tigard, WA 97223',
            ],
            [
                'name'      => 'Fruits Supply',
                'email'     => 'Supplier2@example.com',
                'phone'     => '510-797-5714',
                'country'   => 'USA',
                'city'      => 'Washington',
                'address'   => '4703 Green Avenue Fremont, CA 94536',
            ],
            [
                'name'      => 'Corwin-Pfeffer',
                'email'     => 'supplier3@example.com',
                'phone'     => '240-737-7321',
                'country'   => 'USA',
                'city'      => 'San Francisco',
                'address'   => '4936 Jim Rosa Lane San Francisco, CA 94115',
            ]
        ]);
    }
}
