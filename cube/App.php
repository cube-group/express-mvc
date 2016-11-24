<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/8/26
 * Time: 上午10:42
 */

namespace cube;

use engine\AngularEngine;
use engine\ViewEngine;
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
     * startup multiple kernel mode.
     * @var bool
     */
    private static $multiple = false;
    /**
     * facade router instance.
     * @var Router
     */
    private static $router = null;
    /**
     * cube/Request.
     * @var Request
     */
    private static $req = null;
    /**
     * cube/Response.
     * @var Response
     */
    private static $res = null;

    /**
     * Application GarbageCollection.
     */
    public static function gc()
    {
        if (self::$router) {
            self::$router->gc();
            self::$router = null;
        }
        self::$req = null;
        self::$res = null;

        Log::flush();
    }

    /**
     * set the kernel mode.
     * @param $flag boolean
     */
    public static function multiple($flag)
    {
        if (self::$router) {
            throw new \Exception('App has been initialized!');
        }
        self::$multiple = $flag;
    }

    /**
     * initialize the app.
     *
     * options:[
     *      'base_dir'=>'project dir',
     *      'time_zone'=>'zone',
     *      'time_limit'=>'set_time_limit',
     *      'error_report'=>'0/1',
     *      'debug'=>1
     * ]
     * @param $options array
     */
    public static function init($options)
    {
        if (self::$router) {
            throw new \Exception('App has been initialized!');
        }

        //load libs & modules.
        Config::init($options);

        //check php version.
        if (!Utils::is_legal_php_version('5.6.0')) {
            throw new \Exception('PHP VERSION IS LOW!');
        }

        self::$req = new Request();

        self::$res = new Response();

        self::$router = new Router(self::$req, self::$res);

        //load the logic.
        import(Config::get('core', 'app'));

        //app start.
        self::$router->start();

        //debug
        if ($options && $options['debug']) {
            echo "<pre>";
            print_r(self::$router->stack());
            echo "</pre>";
        }

        //gc.
        self::gc();
    }

    /**
     * add middleWare or path by the facade router.
     *
     * @param array ...$args
     */
    public static function on(...$args)
    {
        switch (count($args)) {
            case 1:
                self::$router->on($args[0]);
                break;
            case 2:
                self::$router->on($args[0], $args[1]);
                break;
            default:
                throw new \Exception('middleWare add error');
                break;
        }
    }

    /**
     * return the facade router.
     *
     * @return Router
     */
    public static function app()
    {
        return self::$router;
    }

    /**
     * create a child app instance.
     *
     * @return Router
     */
    public static function Router()
    {
        return self::$multiple ? Router::createFactory(self::$req, self::$res) : self::$router;
    }

    /**
     * global render the view engine.
     *
     * @param $engine ViewEngine
     * @param $name string
     * @param $value object
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
        'cube/Router.php',
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
     * @param $json array
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
            throw new \Exception('config is error or null');
        }
    }

    /**
     * Get the package.json object children value.
     *
     * @param $args array
     * @return object | null
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
        import([
            'modules/fs/autoload',
            'modules/log/autoload.php',
            'modules/engine/autoload.php'
        ]);
        switch ($e['type']) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                Log::log('Error ' . $e['message']);
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
    import([
        'modules/fs/autoload',
        'modules/log/autoload.php',
        'modules/engine/autoload.php'
    ]);

    Log::log('Exception ' . $e->getMessage());
    $errors = array('msg' => $e->getMessage(), 'level' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile());
    App::globalRender(new AngularEngine(), '500', $errors);
    App::gc();
}

set_error_handler('cube\onErrorHandler');
set_exception_handler('cube\onExceptionHandler');
register_shutdown_function('cube\onErrorHandler');