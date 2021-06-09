<?php

namespace App\Providers;

use App\Helpers\CustomValidators;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('base64_image', function ($attribute, $value, $parameters, $validator) {
            return CustomValidators::validate_base64($value, $parameters);
        });

        Validator::replacer('base64_image', function ($message, $attribute, $rule, $parameters) {
            return 'The image must be in ' . implode(', ', $parameters) . ' format and a maximum of 2048KB';
        });
    }
}
