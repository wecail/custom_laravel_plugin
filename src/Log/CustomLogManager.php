<?php

namespace wecail\custom_laravel_plugin\log;


use wecail\custom_laravel_plugin\log\Formatter\CustomLogFormatter;
use wecail\custom_laravel_plugin\log\Traits\ToolsTraits;
use Illuminate\Log\LogManager;
use Ramsey\Uuid\Uuid;


class CustomLogManager extends LogManager
{
    use ToolsTraits;

    protected $appId;

    protected $userName;

    public function __construct($app, $appId = 'THIRD_SYSTEM', $userName = 'visitor')
    {
        $this->app = $app;
        $this->appId = $appId;
        $this->userName = $userName;

        parent::__construct($app);
    }

    /**
     * 格式化日志信息
     *
     * @return mixed|\Monolog\Formatter\FormatterInterface
     */
    protected function formatter()
    {
        $appId = $this->appId;
        $userName = $this->userName;
        $ip = $this->getRealIp();
        $uuid = $this->getUUID($userName . $ip . microtime(true));

        return tap(new CustomLogFormatter("%datetime%の[%level_name%]の[{$appId}] - [{$userName}:{$ip}][{$uuid}] - [%message%] %context% %extra%\n", 'Y-m-d H:i:s,u'), function ($formatter) {
            $formatter->includeStacktraces();
        });
    }

    /**
     * 生成日志行中的uuid
     *
     * @param string $uuidName
     * @return mixed
     */
    protected function getUUID($uuidName = "")
    {
        $uuid = Uuid::uuid5(Uuid::NAMESPACE_DNS, $uuidName);
        return str_replace('-', null, $uuid);
    }
}
