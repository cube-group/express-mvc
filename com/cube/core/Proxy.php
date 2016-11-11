<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/10/2
 * Time: 下午4:55
 */

namespace com\cube\core;

use com\cube\core\Config;
use com\cube\core\Application;


/**
 * Created by PhpStorm.
 * Observer .
 * User: linyang
 * Date: 16/10/2
 */
final class Model
{
    /**
     * proxy instance array.
     */
    private static $model = null;

    /**
     * proxy config array.
     * @var null
     */
    private static $model_conf = null;

    /**
     * register the proxy instance.
     *
     * @param $name the name of the proxy
     * @param $proxy proxy instance
     */
    public static function register($name, Proxy $proxy)
    {
        self::$model[$name] = $proxy;
    }

    /**
     * execute the model
     *
     * @param $name
     * @return mixed
     */
    public static function get($name, $value = null)
    {
        if (empty(self::$model_conf)) {
            self::$model_conf = Config::get('model');
        }

        if (!empty(self::$model[$name])) {
            return self::$model[$name]->execute($value);
        }

        if (!empty(self::$model_conf)) {
            if (!empty(self::$model_conf[$name])) {
                Config::load(self::$model_conf[$name]);
                return self::$model[$name]->execute($value);
            }
        }

        return null;
    }

    /**
     * remove all models.
     */
    public function gc()
    {
        foreach (self::$model as $key => $value) {
            self::$model[$key]->onRemove();
            unset(self::$model[$key]);
        }
    }
}

/**
 * Class Proxy.
 * Model Layer of the MVC.
 * @package com\cube\framework
 */
abstract class Proxy
{
    /**
     * Application Instance.
     * @var Application
     */
    protected $app;

    /**
     * Proxy constructor.
     */
    public function __construct()
    {
        $this->app = Application::getInstance();

        Model::register($this->getName(), $this);
    }

    /**
     * Model unique string name.
     * @return string
     */
    public function getName()
    {
        return '';//need override
    }

    /**
     * execute the model.
     * @param $value
     */
    public function execute($value)
    {
        //need override.
        return null;
    }

    /**
     * model remove
     */
    public function onRemove()
    {
        //need override.
    }
}