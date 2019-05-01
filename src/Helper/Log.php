<?php

namespace Snowdog\DevTest\Helper;

/**
 * Log into file
 * @package Snowdog\DevTest\Helper
 */
class Log
{
    /**
     * Save error string into file
     * @param $str
     */
    public static function error($str)
    {
        $date = date('Y-m-d H:i:s');
        file_put_contents(APP_BASE_DIR . 'logs/app.log', "ERROR $date : $str\n", FILE_APPEND);
    }
}