<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/8/26
 * Time: 上午10:42
 */

namespace cube;

use engine\AngularEngine;
use log\Log;
use utils\Utils;

/**
 * Class App.
 * Cube HTTP Framework Facade Core Class.
 *
 * @package com\cube\core
 */
final class App
{
    /**
     * connect instance.
     * @var null
     */
    private static $connect = null;
    /**
     * cube/Request.
     * @var null
     */
    private static $req = null;
    /**
     * cube/Response.
     * @var null
     */
    private static $res = null;

    /**
     * Application GarbageCollection.
     */
    public static function gc()
    {
        if (self::$connect) {
            self::$connect->gc();
            self::$connect = null;
        }
        self::$req = null;
        self::$res = null;

        Log::flush();
    }

    /**
     * initialize the app.
     *
     * options:[
     *      'base_dir'=>'project dir',
     *      'time_zone'=>'zone',
     *      'time_limit'=>'set_time_limit',
     *      'error_report'=>'0/1'
     * ]
     * @param $options
     */
    public static function init($options)
    {
        if (self::$connect) {
            throw new \Exception('Error App init!');
        }

        //load libs & modules.
        Config::init($options);

        //check php version.
        if (!Utils::is_legal_php_version('5.6.0')) {
            throw new \Exception('PHP VERSION IS LOW!');
        }

        self::$req = new Request();

        self::$res = new Response();

        self::$connect = new Connect(self::$req, self::$res);

        //load the logic.
        import(Config::get('core', 'app'));

        //app start.
        self::$connect->start();

        //gc.
        self::gc();
    }

    /**
     * create a child app instance.
     *
     * @return mixed
     */
    public static function router()
    {
        return self::$connect;
    }

    /**
     * global render the view engine.
     *
     * @param $engine
     * @param $name
     * @param null $value
     */
    public static function globalRender($engine, $name, $value = null)
    {
        if (self::$res) {
            self::$res->render($engine, $name, $value);
        } else {
            $engine->render('500', $value);
        }
    }

    private function __construct()
    {
        //private
    }
}


/**
 * Class Config.
 * save the Application package.json object.
 * save the global values.
 */
final class Config
{
    /**
     * Framework init dependency package1.
     */
    const LIBS = [
        'cube/Request.php',
        'cube/Response.php',
        'cube/Connect.php',
    ];
    /**
     * cube global config object.
     * @var array
     */
    private static $VALUE = [];

    private function __construct()
    {
    }

    /**
     * append the package.json object info.
     * all constant value.
     *options:[
     *      'base_dir'=>'project dir',
     *      'time_zone'=>'zone',
     *      'time_limit'=>'set_time_limit',
     *      'error_report'=>'0/1'
     * ]
     * @param $json
     * @throws \Exception
     */
    public static function init($options)
    {
        if (!$options) {
            $options = [];
        }
        $options['base_dir'] = $options['base_dir'] ? ($options['base_dir'] . '/') : (__DIR__ . '/../');

        set_time_limit($options['time_limit'] ? $options['time_limit'] : 0);
        error_reporting($options['error_report'] ? $options['error_report'] : 0);
        date_default_timezone_set($options['time_zone'] ? $options['time_zone'] : 'Asia/Shanghai');

        define('BASE_DIR', $options['base_dir']);
        define('START_TIME', microtime(true));

        if ($json = json_decode(file_get_contents($options['base_dir'] . 'package.json'), true)) {
            self::$VALUE = $json;

            define('VIEW_DIR', $options['base_dir'] . $json['dir']['view'] . '/');
            define('TMP_DIR', $options['base_dir'] . $json['dir']['tmp'] . '/');
            define('LOG_PATH', $options['base_dir'] . $json['log']['log']);
            define('LOG_SQL_PATH', $options['base_dir'] . $json['log']['sql']);
            define('CONFIG', $json);

            ini_set('upload_tmp_dir', $options['base_dir'] . $json['dir']['tmp'] . '/');

            import(self::LIBS);
            import($json['modules']);

        } else {
            import(['modules/fs/autoload', 'modules/log/autoload.php', 'modules/engine/autoload.php']);
            throw new \Exception('config is error or null');
        }
    }

    /**
     * Get the package.json object children value.
     *
     * @param $key
     */
    public static function get(...$args)
    {
        switch (count($args)) {
            case 1:
                return self::$VALUE[$args[0]];
                break;
            case 2:
                return self::$VALUE[$args[0]][$args[1]];
                break;
            default:
                return null;
        }
    }
}

function onErrorHandler()
{
    if ($e = error_get_last()) {
        switch ($e['type']) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                //Log::log('Error ' . $e['message']);
                $errors = array('msg' => $e['message'], 'level' => $e['type'], 'line' => $e['line'], 'file' => $e['file']);
                App::globalRender(new AngularEngine(), '500', $errors);
                App::gc();
                break;
        }
    }
}

/**
 * Global Exception Handler.
 * @param Exception $e
 */
function onExceptionHandler(\Exception $e)
{
    //Log::log('Exception ' . $e->getMessage());

    $errors = array('msg' => $e->getMessage(), 'level' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile());
    App::globalRender(new AngularEngine(), '500', $errors);
    App::gc();
}

set_error_handler('cube\onErrorHandler');
set_exception_handler('cube\onExceptionHandler');
register_shutdown_function('cube\onErrorHandler');