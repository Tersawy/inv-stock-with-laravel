<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert(
            [
                'id'                => 1,
                'email'             => 'admin@example.com',
                'currency_id'       => 1,
                'customer_id'       => 1,
                'warehouse_id'      => Null,
                'company_name'      => 'Stocky',
                'company_phone'     => '6315996770',
                'company_address'   => '3618 Abia Martin Drive',
                'footer'            => 'Stocky - Ultimate Inventory With POS',
                'developed_by'      => 'Stocky',
                'company_logo'      => 'logo-default.png'
            ]
        );
    }
}
