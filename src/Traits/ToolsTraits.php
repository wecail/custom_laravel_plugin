<?php

namespace Wecail\CustomLaravelPlugin\Traits;

trait ToolsTraits
{
    public function getRealIp()
    {
        if (app()->runningInConsole()) {
            return null;
        }
        if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
            return trim(current(explode(',', $_SERVER['HTTP_CLIENT_IP'])));
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return trim(current(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])));
        }
        if (isset($_SERVER['HTTP_X_FORWARDED']) && !empty($_SERVER['HTTP_X_FORWARDED'])) {
            return trim(current(explode(',', $_SERVER['HTTP_X_FORWARDED'])));
        }
        return $_SERVER['REMOTE_ADDR'];
    }
}
