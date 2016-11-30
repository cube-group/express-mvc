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
    public static $options = null;

    public static $c_dir = null;
    public static $m_dir = null;

    public static $controllers = [];
    public static $models = [];

    /**
     * create middleware.
     * $options = [
     *      'controller_dir' => 'the dir of controller,such as src/controller',
     *      'controller_prefix' => 'the class prefix of controller,such as src\controller\',
     *      'model_dir' => 'the dir of model,such as src/model',
     *      'model_prefix' => 'the class prefix of model,such as src\model\',
     * ]
     * @param $options
     * @return \Closure
     */
    public static function create($options = null)
    {
        self::$options = !$options ? [] : $options;

        return function ($req = null, $res = null, $next = '') {
            self::$c_dir = (self::$options['controller_dir'] ? self::$options['controller_dir'] : 'controller');
            self::$m_dir = (self::$options['model_dir'] ? self::$options['model_dir'] : 'model');

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
     * @param $className string
     * @param string $method
     * @param null $value
     * @return mixed
     */
    public static function c($className, $method = '', $value = null)
    {
        $absoluteClassName = strtolower($className);

        if (!self::$controllers[$absoluteClassName]) {
            $className = strtoupper(substr($className, 0, 1)) . substr($className, 1);
            $filePath = MVC::$c_dir . '/' . $className . 'Controller.php';

            if (is_file($filePath)) {
                import($filePath);

                $classPrefix = MVC::$options['controller_prefix'] ? (MVC::$options['controller_prefix'] . '\\' . $className . 'Controller') : ($className . 'Controller');
                $instance = new \ReflectionClass($classPrefix);
                self::$controllers[$absoluteClassName] = $instance->newInstance();
            }
        }

        if (MVC::$controllers[$absoluteClassName]) {
            if ($method) {
                $method = $method == '/' ? 'indexAction' : $method . 'Action';
                if (method_exists(MVC::$controllers[$absoluteClassName], $method)) {
                    return MVC::$controllers[$absoluteClassName]->$method($value);
                }
            } else {
                return MVC::$controllers[$absoluteClassName];
            }
        }
        return null;
    }

    /**
     * static get model instance.
     * security mode.
     * @param $modelClassName string
     * @param string $method
     * @param null $value
     * @return mixed|null
     */
    public static function m($modelClassName, $method = '', $value = null)
    {
        $absoluteClassName = strtolower($modelClassName);

        if (!MVC::$models[$absoluteClassName]) {
            $modelClassName = strtoupper(substr($modelClassName, 0, 1)) . substr($modelClassName, 1);
            $filePath = MVC::$m_dir . '/' . $modelClassName . 'Model.php';

            if (is_file($filePath)) {
                import($filePath);

                $classPrefix = MVC::$options['model_prefix'] ? (MVC::$options['model_prefix'] . '\\' . $modelClassName . 'Model') : ($modelClassName . 'Model');
                $instance = new \ReflectionClass($classPrefix);
                MVC::$models[$absoluteClassName] = $instance->newInstance();
            }
        }

        if (MVC::$models[$absoluteClassName]) {
            if ($method) {
                $method = $method == '/' ? 'index' : $method;
                if (method_exists(MVC::$models[$absoluteClassName], $method)) {
                    return MVC::$models[$absoluteClassName]->$method($value);
                }
            } else {
                return MVC::$models[$absoluteClassName];
            }
        }
        return null;
    }

    /**
     * garbage collection.
     */
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
}

/**
 * Class MVC_Controller
 * MVC-C
 * @package modules
 */
class MVC_Controller
{
    /**
     * model instance of this controller.
     * @var MVC_Model
     */
    protected $model = null;

    /**
     * MVC_Controller constructor.
     */
    public function __construct()
    {
        $className = strtolower(end(explode('\\', get_class($this))));
        $className = current(explode('controller', $className));

        $this->model = $this->model($className);
    }

    /**
     * get model instance.
     * @param $modelClassName string
     * @param string $modelMethodName
     * @param null $value
     * @return mixed|null
     */
    protected function model($modelClassName, $modelMethodName = '', $value = null)
    {
        return MVC::m($modelClassName, $modelMethodName, $value);
    }
}