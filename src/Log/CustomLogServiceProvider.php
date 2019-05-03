<?php

namespace wecail\custom_laravel_plugin\log;


use Illuminate\Log\LogServiceProvider;


class CustomLogServiceProvider extends LogServiceProvider
{
    protected $appId;

    protected $userName;

    public function register()
    {
        $this->app->singleton('log', function ($app) {
            return new CustomLogManager($app);
        });
    }
}
