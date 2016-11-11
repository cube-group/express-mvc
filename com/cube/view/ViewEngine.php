<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/8/30
 * Time: 下午2:41
 */

namespace com\cube\view;

use com\cube\core\Application;
use com\cube\core\Config;
use com\cube\fs\FS;

/**
 * Class ViewEngine
 * @package com\cube\view
 *
 */
class ViewEngine
{

    /**
     * 框架实例引用.
     * @var
     */
    protected $app;

    public function __construct()
    {
        //need override.
        $this->app = Application::getInstance();
    }

    /**
     * @param $name file name viewdir
     * @param $data array data
     */
    public function render($name, $data = null)
    {
        return FS::read($this->getViewPagePath($name));
    }

    /**
     * 获取view page 文件地址.
     * @param $name
     * @return string
     */
    final protected function getViewPagePath($name)
    {
        return Config::$VALUE['VIEW'] . $name . ".html";
    }
}