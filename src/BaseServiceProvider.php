<?php

namespace Wecail\CustomLaravelPlugin;


use Wecail\CustomLaravelPlugin\Log\CustomLogServiceProvider;
use Wecail\CustomLaravelPlugin\Cache\CustomRedisServiceProvider;
use Illuminate\Support\ServiceProvider;


class BaseServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerLogService();
        $this->registerRedisClusterService();
    }

    /**
     * Register the custom format Log services.
     *
     * @return void
     */
    protected function registerLogService()
    {
        $this->app->register(new CustomLogServiceProvider($this->app));
    }

    protected function registerRedisClusterService(){
        $this->app->register(new CustomRedisServiceProvider($this->app));
    }

    protected function registerCacheService(){

    }

    protected function registerQueueService(){

    }

    protected function registerOtherService(){

    }

    /**
     * 获取应用编号
     * @return string
     */
    protected function getAppId()
    {
        //@todo
        return "A01_WEB";
    }

    /**
     * 获取用户名
     * @return string
     */
    protected function getUserName()
    {
        //@todo
        return "fmarko";
    }
}
