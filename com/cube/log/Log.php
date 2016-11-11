<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/10/9
 * Time: 下午6:02
 */

namespace com\cube\log;

use com\cube\core\Config;
use com\cube\fs\FS;

/**
 * Class Log.
 * Application Cube Log System.
 * @package com\cube\log
 */
final class Log
{
    /**
     * log string.
     * @var string
     */
    private static $logs = '';
    /**
     * mysql query log string.
     * @var string
     */
    private static $mysql_logs = '';

    /**
     * Append info log to log string.
     * @param $value
     * @param $displayDuration
     */
    public static function log($value, $displayDuration = false)
    {
        if ($displayDuration) {
            self::$logs .= '[' . date('Y-m-d H:i:s', time()) . '] ' . $value . ' (' .Config::getTimer() . "ms)\t\n";
        } else {
            self::$logs .= '[' . date('Y-m-d H:i:s', time()) . '] ' . $value . "\t\n";
        }
    }

    /**
     * Append error log to log string.
     * @param $value
     */
    public static function error($value)
    {
        self::$logs .= '[' . date('Y-m-d H:i:s', time()) . '] Error ' . $value . "\t\n";
    }

    /**
     * Append exception log to log string.
     * @param $value
     */
    public static function exception($value)
    {
        self::$logs .= '[' . date('Y-m-d H:i:s', time()) . '] Exception ' . $value . "\t\n";
    }

    /**
     * Append sql log to sql log string.
     * @param $value
     */
    public static function mysql($value)
    {
        self::$mysql_logs .= '[' . date('Y-m-d H:i:s', time()) . '] ' . $value . "\t\n";
    }

    /**
     * Flush the log and sql log.
     */
    public static function flush()
    {
        if (!empty(self::$logs)) {
            FS::append(Config::get('log', 'log'), self::$logs);
            self::$logs = '';
        }

        if (!empty(self::$mysql_logs)) {
            FS::append(Config::get('log', 'mysql'), self::$mysql_logs);
            self::$mysql_logs = '';
        }
    }
}