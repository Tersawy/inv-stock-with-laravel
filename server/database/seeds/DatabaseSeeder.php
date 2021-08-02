<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(SupplierSeeder::class);
        $this->call(UnitSeeder::class);
        $this->call(BrandSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(ProductVariantSeeder::class);
        $this->call(WarehouseSeeder::class);
        $this->call(ProductWarehouseSeeder::class);
        $this->call(CustomerSeeder::class);
        $this->call(CurrencySeeder::class);
        $this->call(SettingSeeder::class);
    }
}
