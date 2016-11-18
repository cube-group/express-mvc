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
     * @param $options
     * @return \Closure
     */
    public static function create($options)
    {
        self::$options = $options;

        return function ($req = null, $res = null, $next = '') {
            if (!defined('BASE_DIR')) {
                throw new \Exception('BASE_DIR not defined!');
            }
            $base_dir = constant('BASE_DIR');
            $controller_dir = (self::$options['controller'] ? self::$options['controller'] : 'controller');
            $model_dir = (self::$options['model'] ? self::$options['model'] : 'model');

            if (!is_dir($controller_dir) || !is_dir($model_dir)) {
                throw new \Exception('controller or model dir not exists!');
            }

            self::$c_dir = [$controller_dir, $base_dir . $controller_dir];
            self::$m_dir = [$model_dir, $base_dir . $model_dir];

            if ($next) {
                $next();
            }
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
            list($c_path, $c_dir) = self::$c_dir;
            $filePath = $c_dir . '/' . $className . 'Controller.php';

            if (is_file($filePath)) {
                import($filePath);

                $instance = new \ReflectionClass($c_path . '\\' . $className . 'Controller');
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
            list($m_path, $m_dir) = MVC::$m_dir;
            $filePath = $m_dir . '/' . $className . 'Model.php';

            if (is_file($filePath)) {
                import($filePath);

                $instance = new \ReflectionClass($m_path . '\\' . $className . 'Model');
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