<?php

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'fullname' => "customer",
                'phone' => "123456678",
                'username' => "customer",
                'email' => "customer@gmail.com",
                'type' => User::CUSTOMER_TYPE,
                'password' => bcrypt("123123123"),
            ],
            [
                'fullname' => "owner",
                'phone' => "123456678",
                'username' => "owner",
                'email' => "owner@gmail.com",
                'type' => User::OWNER_TYPE,
                'password' => bcrypt("123123123"),
            ],
            [
                'fullname' => "admin",
                'phone' => "123456678",
                'username' => "admin",
                'email' => "admin@gmail.com",
                'type' => User::ADMIN_TYPE,
                'password' => bcrypt("123123123"),
            ],
            [
                'fullname' => "user",
                'phone' => "123456678",
                'username' => "user",
                'email' => "user@gmail.com",
                'type' => User::USER_TYPE,
                'password' => bcrypt("123123123"),
            ]
        ]);
    }
}
