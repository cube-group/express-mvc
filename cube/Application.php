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
 * Class Application.
 * Cube HTTP Framework Facade Core Class.
 * @package com\cube\core
 */
final class Application
{
    /**
     * core connect router.
     * @var
     */
    private static $connect;

    private static $instance;

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new Application();
        }
        return self::$instance;
    }

    /**
     * Application GarbageCollection.
     */
    public static function gc()
    {
        if (!empty(self::$connect)) {
            self::$connect->gc();
        }
        Log::flush();
    }

    /**
     * initialize Application Framework.
     * @param $www_dir the dir of the project
     */
    public static function init($www_dir)
    {
        if (!empty(self::$instance)) {
            throw new \Exception('Application exists');
        }

        //load libs & engine & modules.
        Config::init($www_dir, 'Asia/Shanghai');

        //check php version.
        if (!Utils::is_legal_php_version('5.6.0')) {
            throw new \Exception('PHP VERSION IS LOW.');
        }

        return self::getInstance();
    }

    /**
     * start Application.
     * @throws \Exception
     */
    public function start()
    {
        if (!empty(self::$connect)) {
            throw new \Exception('Application has been started');
        }

        //init connect.
        self::$connect = new Connect(new Request(), new Response());
        //load the logic.
        Config::load(Config::get('core', 'app'));
        //start connect.
        self::$connect->start();
        //garbageCollection.
        self::gc();
    }

    /**
     * get the core connect.
     * @return mixed
     */
    public static function router()
    {
        return self::$connect;
    }

    /**
     * Global Render the view engine.
     *
     * @param $engine
     * @param $name
     * @param null $value
     */
    public static function globalRender($engine, $name, $value = null)
    {
        if (!empty(self::$connect)) {
            self::$connect->res->render($engine, $name, $value);
        } else {
            $engine->render('500', $value);
        }
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
        'cube/Connect.php',
        'cube/Request.php',
        'cube/Response.php',
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
     * Load the php file in the cube framework.
     *
     * @param $files
     */
    public static function load($files)
    {
        $arr = null;
        if (empty($files)) {
            return;
        } elseif (is_array($files)) {
            $arr = $files;
        } else {
            $arr = array($files);
        }
        $base_dir = defined('BASE_DIR') ? constant('BASE_DIR') : '';
        foreach ($arr as $file) {
            require_once $base_dir . $file;
        }
    }

    /**
     * append the package.json object info.
     *
     * @param $json
     * @throws \Exception
     */
    public static function init($www_dir, $time_zone)
    {
        self::$VALUE['BASE_DIR'] = $www_dir . '/';
        define('BASE_DIR', self::$VALUE['BASE_DIR']);
        define('START_TIME', microtime(true));
        date_default_timezone_set($time_zone);

        $json = json_decode(file_get_contents(self::$VALUE['BASE_DIR'] . 'package.json'), true);
        if (!empty($json)) {
            foreach (self::$VALUE as $key => $value) {
                $json[$key] = $value;
            }
            self::$VALUE = $json;

            define('VIEW_DIR', self::$VALUE['BASE_DIR'] . self::$VALUE['dir']['view'] . '/');
            define('TMP_DIR', self::$VALUE['BASE_DIR'] . self::$VALUE['dir']['tmp'] . '/');
            define('DOWNLOAD_DIR', self::$VALUE['BASE_DIR'] . self::$VALUE['dir']['download'] . '/');
            define('LOG_PATH',self::$VALUE['BASE_DIR'] . self::$VALUE['log']['log']);
            define('LOG_SQL_PATH',self::$VALUE['BASE_DIR'] . self::$VALUE['log']['sql']);
            define('CONFIG', $json);

            ini_set('upload_tmp_dir', constant('TMP_DIR'));

            self::load(self::LIBS);
            self::load($json['modules']);

        } else {
            throw new \Exception('config is error or null!');
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


//error_reporting(0);

function onErrorHandler()
{
    if ($e = error_get_last()) {
        switch ($e['type']) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                Log::log('Error ' . $e['message']);
                $errors = array('msg' => $e['message'], 'level' => $e['type'], 'line' => $e['line'], 'file' => $e['file']);
                Application::globalRender(new AngularEngine(), '500', $errors);
                Application::gc();
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
    Log::log('Exception ' . $e->getMessage());

    $errors = array('msg' => $e->getMessage(), 'level' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile());
    Application::globalRender(new AngularEngine(), '500', $errors);
    Application::gc();
}

set_error_handler('cube\onErrorHandler');
set_exception_handler('cube\onExceptionHandler');
register_shutdown_function('cube\onErrorHandler');