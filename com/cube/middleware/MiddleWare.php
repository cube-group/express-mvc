<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/8/26
 * Time: 上午11:02
 */

namespace com\cube\middleware;

use com\cube\core\Application;
use com\cube\core\Request;
use com\cube\core\Response;

/**
 * Class MiddleWare 框架内部中间件基类
 * @package com\cube\core
 */
abstract class MiddleWare
{
    /**
     * 框架实例引用.
     * @var
     */
    protected $app;

    /**
     * MiddleWare constructor.
     * 必须要执行.
     */
    public function __construct()
    {
        $this->app = Application::getInstance();
    }

    /**
     * 进行到当前中间件.
     * @param Request $req
     * @param Response $res
     */
    public function run(Request $req, Response $res)
    {
    }

    /**
     * 中间件执行完毕.
     */
    public function end()
    {
        //need override.
    }
}