<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/11/17
 * Time: 上午10:10
 */
namespace mvc;

/**
 * Class MVC.
 * Class mapping and reflect.
 * @package modules
 */
class MVC
{
    private static $options = null;

    public static $c_dir = null;
    public static $m_dir = null;

    public static $controllers = [];
    public static $models = [];

    /**
     * create middleware.
     * @param $controllerDir
     * @param $modelDir
     * @param $safe set the safe-mode,default is safety.
     * @return \Closure
     */
    public static function create($controllerDir, $modelDir, $safe = true)
    {
        self::$options = ['controller' => $controllerDir, 'model' => $modelDir, 'safe' => $safe];

        return function ($req = null, $res = null, $next = '') {
            self::$c_dir = (self::$options['controller'] ? self::$options['controller'] : 'controller');
            self::$m_dir = (self::$options['model'] ? self::$options['model'] : 'model');

            if (!is_dir(self::$c_dir) || !is_dir(self::$m_dir)) {
                throw new \Exception('controller or model dir not exists!');
            }

            if ($next) {
                $next();
            }

            //gc
            self::gc();
        };
    }

    /**
     * find and load ControllerClass ,then execute its method.
     * @param $className
     * @param $method
     * @param null $value
     * @return mixed
     */
    public static function c($className, $method, $value = null)
    {
        if (!self::$controllers[$className]) {
            $className = strtoupper(substr($className, 0, 1)) . substr($className, 1);
            $filePath = MVC::$c_dir . '/' . $className . 'Controller.php';

            if (is_file($filePath)) {
                import($filePath);

                $instance = new \ReflectionClass(MVC::$c_dir . '\\' . $className . 'Controller');
                self::$controllers[$className] = $instance->newInstance($className);
            }
        }

        if (!self::$controllers[$className]) {
            return null;
        }
        if (method_exists(self::$controllers[$className], $method)) {
            return self::$controllers[$className]->$method($value);
        }
        return null;
    }

    private function gc()
    {
        self::$options = null;

        self::$c_dir = null;
        self::$m_dir = null;

        self::$controllers = null;
        self::$models = null;
    }
}

/**
 * Class MVC_Model.
 * MVC-M
 * @package modules
 */
class MVC_Model
{
    /**
     * class name
     * @var string
     */
    protected $className = '';
}

/**
 * Class MVC_Controller
 * MVC-C
 * @package modules
 */
class MVC_Controller
{
    /**
     *
     * @var string
     */
    protected $className = '';

    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * get the model proxy instance.
     * @param $method
     * @param null $value
     * @param null $className
     */
    public function model($method, $value = null)
    {
        $className = $this->className;
        if (!MVC::$models[$className]) {
            $filePath = MVC::$m_dir. '/' . $className . 'Model.php';

            if (is_file($filePath)) {
                import($filePath);

                $instance = new \ReflectionClass(MVC::$m_dir . '\\' . $className . 'Model');
                MVC::$models[$className] = $instance->newInstance($className);
            }
        }

        if (!MVC::$models[$className]) {
            return null;
        }
        if (method_exists(MVC::$models[$className], $method)) {
            return MVC::$models[$className]->$method($value);
        }
        return null;
    }
}