<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;

class ValidationServiceProvider extends ServiceProvider
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
        Validator::resolver(function($translator, $data, $rules, $messages)
        {
            return new \App\Services\Validation($translator, $data, $rules, $messages);
        });
    }

}
