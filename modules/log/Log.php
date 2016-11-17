<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/10/9
 * Time: 下午6:02
 */

namespace log;

use fs\FS;

/**
 * Class Log.
 * @package cube\log.log
 */
final class Log
{
    /**
     * log.log string.
     * @var string
     */
    private static $logs = '';
    /**
     * mysql query log.log string.
     * @var string
     */
    private static $mysql_logs = '';

    /**
     * Append info log.log to log.log string.
     * @param $value
     */
    public static function log($value, $time = -1)
    {
        if ($time >= 0) {
            self::$logs .= '[' . date('Y-m-d H:i:s', time()) . '] ' . $value . '(' . $time . ' ms)' . "\n";
        } else {
            self::$logs .= '[' . date('Y-m-d H:i:s', time()) . '] ' . $value . "\n";
        }

    }

    /**
     * Append error log.log to log.log string.
     * @param $value
     */
    public static function error($value)
    {
        self::$logs .= '[' . date('Y-m-d H:i:s', time()) . '] Error ' . $value . "\n";
    }

    /**
     * Append exception log.log to log.log string.
     * @param $value
     */
    public static function exception($value)
    {
        self::$logs .= '[' . date('Y-m-d H:i:s', time()) . '] Exception ' . $value . "\n";
    }

    /**
     * Append sql.log log.log to sql.log log.log string.
     * @param $value
     */
    public static function mysql($value)
    {
        self::$mysql_logs .= '[' . date('Y-m-d H:i:s', time()) . '] ' . $value . "\n";
    }

    /**
     * Flush the log.log and sql.log log.log.
     */
    public static function flush()
    {
        if (!empty(self::$logs)) {
            FS::append(constant('LOG_PATH'), self::$logs);
            self::$logs = '';
        }

        if (!empty(self::$mysql_logs)) {
            FS::append(constant('LOG_SQL_PATH'), self::$mysql_logs);
            self::$mysql_logs = '';
        }
    }
}