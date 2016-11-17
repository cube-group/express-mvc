<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/8/26
 * Time: 上午10:42
 */

namespace cube\core;

use cube\log\Log;
use cube\utils\SystemUtil;
use cube\middleware\Connect;
use cube\view\AngularEngine;
use cube\view\ViewEngine;

/**
 * Class Application.
 * Cube HTTP Framework Facade Core Class.
 * @package com\cube\core
 */
final class Application
{
    /**
     * Framework init dependency package1.
     */
    const INIT_LIBS = [
        'cube/log/Log.php',
        'cube/core/Config.php',
        'cube/utils/SystemUtil.php',
        'cube/utils/URLUtil.php',
        'cube/fs/FS.php',
        'cube/view/ViewEngine.php',
        'cube/view/EchoEngine.php',
        'cube/view/AngularEngine.php'
    ];

    /**
     * Framework init dependency package2.
     */
    const COMMON_LIBS = [
        'cube/http/Http.php',
        'cube/core/Connect.php',
        'cube/core/Request.php',
        'cube/core/Response.php',
    ];

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

        foreach (self::INIT_LIBS as $key => $path) {
            require_once $www_dir . '/' . $path;
        }

        Config::set('BASE_DIR', $www_dir);
        Config::set('TIME_ZONE', 'Asia/Shanghai');

        //check php version.
        if (!SystemUtil::check_version()) {
            throw new \Exception('PHP VERSION IS LOW.', CubeException::$VERSION_ERROR);
        }
        //check exts.
        $unknown_ext = SystemUtil::check_unknown_extension();
        if (!empty($unknown_ext)) {
            throw new \Exception('Unknown Ext ' . $unknown_ext, CubeException::$EXT_ERROR);
        }

        Config::init();
        //load libs & engine & modules.
        Config::load(self::COMMON_LIBS);
        Config::load(Config::get('modules'));

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
     * @param ViewEngine $engine
     * @param $name
     * @param null $value
     */
    public static function globalRender(ViewEngine $engine, $name, $value = null)
    {
        if (!empty(self::$connect)) {
            self::$connect->res->render($engine, $name, $value);
        } else {
            $engine->render('500', $value);
        }
    }
}


/**
 * Class BaseDynamic.
 * @package cube\core
 */
class DynamicClass
{
    protected $body;

    public function __construct()
    {
        $this->body = array();
    }

    public function __get($name)
    {
        // TODO: Implement __get() method.
        return $this->body[$name];
    }

    public function __set($name, $value)
    {
        // TODO: Implement __set() method.
        $this->body[$name] = $value;
    }

    public function delete($name)
    {
        unset($this->body[$name]);
    }

    public function clear()
    {
        foreach ($this->body as $key => $value) {
            unset($this->body[$key]);
        }
    }
}


error_reporting(1);

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

set_error_handler('cube\core\onErrorHandler');
set_exception_handler('cube\core\onExceptionHandler');
register_shutdown_function('cube\core\onErrorHandler');