<?php

namespace Wecail\CustomLaravelPlugin\Cache;


use Illuminate\Redis\RedisManager;
use Illuminate\Redis\RedisServiceProvider;
use Illuminate\Support\Arr;

class CustomRedisServiceProvider extends RedisServiceProvider
{
    public function register()
    {
        $this->app->singleton('redis', function ($app) {
            $config = $this->getRedisConfig($app);

            return new RedisManager($app, Arr::pull($config, 'client', 'predis'), $config);
        });

        $this->app->bind('redis.connection', function ($app) {
            return $app['redis']->connection();
        });
    }

    /**
     * 取得 redis 设定
     *
     * @param $app
     * @return array
     */
    protected function getRedisConfig($app)
    {
        $config = $app->make('config')->get('database.redis');

        if ($cluster = $this->getClusterConfig($config)) {
            return array_merge(Arr::only($config, ['client']), $cluster);
        }

        return Arr::except($config, ['cluster']);
    }

    /**
     * 组合 Redis Cluster 资料
     * @param $config
     * @return array
     */
    protected function getClusterConfig($config) : array
    {
        if (! $cluster = Arr::pull($config, 'cluster', null)) {
            return [];
        }

        return [
            'options' => $this->getClusterOption($password = $config['cluster_password']),
            'clusters' => [
                'default' => $this->getClusterSettings($cluster, $password)
            ]
        ];
    }

    /**
     * 取得设定
     * @param $password
     * @param array $options
     * @return array
     */
    protected function getClusterOption($password, array $options = array())
    {
        return [
            'cluster' => 'redis',
            'parameters' => array_merge([
                'password' => $password
            ], $options)
        ];
    }

    /**
     * 组合 cluster
     * @param string $cluster
     * @param null $password
     * @return array
     */
    protected function getClusterSettings(string $cluster = "", $password = null) : array
    {
        $paths = array_filter(explode( ";", $cluster));

        $clusters = [];
        foreach ($paths as $path) {
            list($host, $port) = array_filter(explode(":", $path));

            $clusters[] = [
                'host'     => trim($host),
                'port'     => trim($port),
                'database' => 0,
                'password' => $password
            ];
        }

        return $clusters;
    }
}
