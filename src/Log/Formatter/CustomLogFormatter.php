<?php

namespace wecail\custom_laravel_plugin\log\Formatter;


use Monolog\Formatter\LineFormatter;

class CustomLogFormatter extends LineFormatter
{
    protected $filters;

    public function __construct($format = null,  $dateFormat = null, $allowInlineLineBreaks = false, $ignoreEmptyContextAndExtra = false)
    {
        $this->setFilter();
        parent::__construct($format, $dateFormat, $allowInlineLineBreaks, $ignoreEmptyContextAndExtra);
    }


    protected function setFilter(){
        $config_filters = config('logging.filters', null);

        $filters = [
            'realname',
            'real_name',
            'realName',
            'password',
            'phone',
            'tel',
            'telphone',
            'telPhone',
            'address',
            'email',
            'Email',
            'bankAccountName',
            'bankAccountNo',
            'depositBy',
            'depositor',
            'pt_key'
        ];

        //如果无自定义过滤敏感信息设置，则使用默认的设置
        if($config_filters){
            $filters = explode(',', $config_filters);
        }

        $this->filters = $filters;
    }

    /**
     * {@inheritdoc}
     */
    public function format(array $record)
    {
        $output = parent::format($record);

        $output = $this->filterSensitivePart($output);

        return $output;
    }

    //替换敏感信息
    protected function filterSensitivePart($message)
    {
        $patternWithKey = [];
        $replacementWithKey = [];

        $patternWithContent = [
            '/([456][\d]{14,})/i', //银行卡（15+）
            '/(1[34578][\d]{9,10})/i', //手机号（11-12位数）
            '/(?:[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/i', //邮箱，RFC5322
        ];

        $replacementWithContent = [
            '***可疑银行卡号***',
            '***可疑手机号***',
            '***可疑邮箱***',
        ];

        foreach ($this->filters as $filter){
            array_push($patternWithKey, '/"'. $filter. '":"([^,]+)"/');
            array_push($patternWithKey, '/'. $filter. '=(\w{0,})/');
            array_push($replacementWithKey, '"'. $filter. '":"***可疑敏感信息***"');
            array_push($replacementWithKey, $filter. '=***可疑敏感信息***');
        }

        $message = preg_replace($patternWithKey, $replacementWithKey, $message);
        $message = preg_replace($patternWithContent, $replacementWithContent, $message);

        return $message;
    }
}