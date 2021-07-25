<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // $attr = $request->validate([
        //     'fullname' => 'required|string|max:255',
        //     'username' => 'required|string|max:255|unique:users,username',
        //     'phone' => 'required|string|max:255|unique:users,phone',
        //     'email' => 'required|string|email|unique:users,email',
        //     'password' => 'required|string|min:8|confirmed'
        // ]);

        User::create([
            'fullname' => "customer",
            'phone' => "123456678",
            'username' => "customer",
            'email' => "customer@gmail.com",
            'type' => User::CUSTOMER_TYPE,
            'password' => bcrypt("123123123"),
        ]);

        User::create([
            'fullname' => "owner",
            'phone' => "123456678",
            'username' => "owner",
            'email' => "owner@gmail.com",
            'type' => User::OWNER_TYPE,
            'password' => bcrypt("123123123"),
        ]);

        User::create([
            'fullname' => "admin",
            'phone' => "123456678",
            'username' => "admin",
            'email' => "admin@gmail.com",
            'type' => User::ADMIN_TYPE,
            'password' => bcrypt("123123123"),
        ]);

        User::create([
            'fullname' => "user",
            'phone' => "123456678",
            'username' => "user",
            'email' => "user@gmail.com",
            'type' => User::USER_TYPE,
            'password' => bcrypt("123123123"),
        ]);
    }


    public function login(Request $request)
    {
        $attr = $request->validate([
            'email' => 'required|string|email|',
            'password' => 'required|string|min:8'
        ]);

        if (!Auth::attempt($attr)) {
            return $this->error('Credentials not match', 401);
        }

        return $this->success([
            'token' => auth()->user()->createToken($request->device_name ?? "API_TOKEN")->plainTextToken,
            'user' => auth()->user()
        ]);
    }


    public function me(Request $req)
    {
        return $this->success(auth()->user());
    }


    public function logout(Request $req)
    {
        auth()->user()->tokens()->delete();

        return $this->success([ 'message' => 'Tokens Revoked' ]);
    }
}
