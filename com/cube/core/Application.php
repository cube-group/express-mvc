<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/8/26
 * Time: 上午10:42
 */

namespace com\cube\core;

use com\cube\error\CubeException;
use com\cube\log\Log;
use com\cube\utils\SystemUtil;
use com\cube\middleware\Connect;
use com\cube\view\EchoEngine;
use com\cube\view\AngularEngine;

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
        'com/cube/log/Log.php',
        'com/cube/core/Config.php',
        'com/cube/utils/SystemUtil.php',
        'com/cube/error/CubeException.php',
        'com/cube/fs/FS.php',
        'com/cube/view/ViewEngine.php',
        'com/cube/view/EchoEngine.php',
        'com/cube/view/AngularEngine.php'
    ];

    /**
     * Framework init dependency package2.
     */
    const COMMON_LIBS = [
        'com/cube/international/International.php',
        'com/cube/utils/ArrayUtil.php',
        'com/cube/utils/URLUtil.php',
        'com/cube/db/DB.php',
        'com/cube/http/Http.php',
        'com/cube/middleware/Connect.php',
        'com/cube/middleware/MiddleWare.php',
        'com/cube/core/BaseDynamic.php',
        'com/cube/core/Request.php',
        'com/cube/core/Response.php',
        'com/cube/core/IBody.php',
        'com/cube/core/ISession.php',
        'com/cube/core/Proxy.php',
    ];

    /**
     * Http Input Stream Instance.
     * @var Request
     */
    public $request;
    /**
     * Http Output Stream Instance.
     * @var Response
     */
    public $response;
    /**
     * 中间件管理器.
     * @var Router
     */
    private static $connect;

    private static $instance;

    /**
     * Simple Single Instance.
     * @return Application
     */
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
        if (!empty(self::$instance)) {
            if (!empty(self::$instance->notifier)) {
                self::$instance->notifier->gc();
            }
            if (!empty(self::$instance->connect)) {
                self::$instance->connect->gc();
            }
        }

        Log::flush();
    }

    /**
     * Get the connect instance,
     * @return Router
     */
    public static function router()
    {
        return self::$connect;
    }

    /**
     * Application constructor.
     */
    private function __construct()
    {
    }

    /**
     * initialize Application Framework.
     * @param $www_dirF
     * @param $conf_path
     */
    public function init($www_dir)
    {
        foreach (self::INIT_LIBS as $key => $path) {
            require_once $www_dir . '/' . $path;
        }

        Config::set('BASE_DIR', $www_dir . '/');
        Config::set('TIME_ZONE', 'Asia/Shanghai');

        //check php version.
        if (!SystemUtil::check_version()) {
            throw new CubeException('PHP VERSION IS LOW.', CubeException::$VERSION_ERROR);
        }
        //check exts.
        $unknown_ext = SystemUtil::check_unknown_extension();
        if (!empty($unknown_ext)) {
            throw new CubeException('Unknown Ext ' . $unknown_ext, CubeException::$EXT_ERROR);
        }

        Config::init();

        //load engine & modules.
        //load libs.
        Config::load(self::COMMON_LIBS);
        Config::load(Config::get('engine'));
        Config::load(Config::get('modules'));

        //init Request.
        $this->request = new Request();
        //init Response.
        $this->response = new Response();
        //init connect.
        self::$connect = new Connect($this->request, $this->response, $this);

        return $this;
    }

    /**
     * Start to execute all routerMiddleWares.
     */
    public function startup()
    {
        if (Config::get('START')) {
            throw new CubeException(CubeException::$UNKNOW_ERROR);
        }
        Config::set('START', true);
        Config::load(Config::get('core', 'app'));

        self::$connect->restart();
        //garbageCollection.
        self::gc();
    }

    /**
     * Connect All RouterMiddleWare Not Catch Filter.
     */
    public function onCatch404()
    {
        $this->response->render(new EchoEngine(), '404');
    }
}

//error_reporting(0);
/**
 * Global Error Handler.
 * @param $error_level
 * @param $error_message
 * @param $error_file
 * @param $error_line
 * @param $error_context
 */
function onErrorHandler($error_level, $error_message, $error_file, $error_line, $error_context)
{
    switch ($error_level) {
        case E_USER_NOTICE://提醒级别
        case E_WARNING: //警告级别
        case E_USER_WARNING: //警告级别
        case E_ERROR://错误级别
        case E_USER_ERROR://错误级别
            break;
        default:
            return;
    }

    Log::log('Error ' . $error_message);

    $errors = array('msg' => $error_message, 'trace' => 'ErrorLevel: ' . $error_level . '<br>ErrorContext: ' . serialize($error_context) . '<br>ErrorLine: ' . $error_line . '<br>ErrorFile: ' . $error_file);
    if (!empty(Application::getInstance()->response)) {
        Application::getInstance()->response->render(new AngularEngine(), '500', $errors);
    } else {
        $viewEngine = new AngularEngine();
        $viewEngine->render('500', $errors);
    }

    Application::gc();
}

/**
 * Global Exception Handler.
 * @param Exception $e
 */
function onExceptionHandler(\Exception $e)
{
    Log::log('Exception ' . $e->getMessage());

    $errors = array('msg' => $e->getMessage(), 'trace' => $e->getTraceAsString());
    if (!empty(Application::getInstance()->response)) {
        Application::getInstance()->response->render(new AngularEngine(), '500', $errors);
    } else {
        $viewEngine = new AngularEngine();
        $viewEngine->render('500', $errors);
    }

    Application::gc();
}

/**
 * Code Execute End Error Check.
 */
function onShutDownHandler()
{
    if (error_get_last()) {
        Log::log('ShutDownError ' . error_get_last());
    }
}

set_error_handler('com\cube\core\onErrorHandler');
set_exception_handler('com\cube\core\onExceptionHandler');
register_shutdown_function('com\cube\core\onShutDownHandler');