<?php

/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/11/8
 * Time: 4:55 pm
 */
namespace com\cube\core;
use com\cube\fs\FS;

/**
 * Class Config.
 * save the Application package.json object.
 * save the global values.
 */
final class Config
{
    /**
     * 全局配置.
     * @var array
     */
    public static $VALUE = [
        'CONFIG' => null,
        'WWW' => '/',
        'VIEW' => '/view',
        'TMP' => '/tmp',
        'UPLOAD' => '/upload',
        'START_TIME' => 0
    ];

    private function __construct()
    {
    }

    /**
     * append the package.json object info.
     *
     * @param $json
     * @throws \Exception
     */
    public static function init()
    {
        $json = json_decode(FS::read(self::$VALUE['WWW'] . 'package.json'), true);
        if (!empty($json)) {
            self::$VALUE['CONFIG'] = $json;
            self::$VALUE['VIEW'] = self::$VALUE['WWW'] . $json['dir']['view'];
            self::$VALUE['TMP'] = self::$VALUE['WWW'] . $json['dir']['tmp'];
            self::$VALUE['UPLOAD'] = self::$VALUE['WWW'] . $json['dir']['upload'];
        } else {
            throw new \Exception('config is error or null!');
        }
    }

    /**
     * Get the package.json object children value.
     *
     * @param $key
     */
    public static function get(...$keys)
    {
        if (!empty(self::$VALUE['CONFIG'])) {
            switch (count($keys)) {
                case 0:
                    return null;
                    break;
                case 1:
                    return self::$VALUE['CONFIG'][$keys[0]];
                    break;
                case 2:
                    return self::$VALUE['CONFIG'][$keys[0]][$keys[1]];
                    break;
            }
        }
        return null;
    }

    /**
     * Set the global value.
     *
     * ['START'=>bool,'TIME'=>microtime(true),'TIME_ZONE'=>'']
     * @param $key
     * @param $value
     */
    public static function set($key, $value)
    {
        if (!empty($key)) {
            switch ($key) {
                case 'BASE_DIR':
                    self::$VALUE['WWW'] = $value;
                    break;
                case 'TIME_ZONE':
                    self::$VALUE['START_TIME'] = microtime(true);
                    date_default_timezone_set($value);
                    break;
            }
        }
    }

    /**
     * Load the php file in the cube framework.
     *
     * @param $options
     */
    public static function load($options)
    {
        $arr = null;
        if (empty($options)) {
            return;
        } elseif (is_array($options)) {
            $arr = $options;
        } else {
            $arr = array($options);
        }
        foreach ($arr as $key => $path) {
            require_once self::$VALUE['WWW'] . $path;
        }
    }

    /**
     * Get the application run duration microtime.
     *
     * @return int
     */
    public static function getTimer()
    {
        return intval((microtime(true) - self::$VALUE['START_TIME']) * 1000);
    }
}