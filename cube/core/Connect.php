<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/8/26
 * Time: 上午9:59
 */

namespace cube\middleware;

use cube\core\Config;
use cube\view\EchoEngine;

/**
 * Class Connect.
 * MiddleWare Controller.
 * @package com\cube\middleware
 */
final class Connect
{
    /**
     * request instance reference.
     * @var
     */
    public $req;
    /**
     * response instance reference.
     * @var
     */
    public $res;
    /**
     * next function instance.
     * @var
     */
    private $connectNext;
    /**
     * middleWare stack.
     * @var array
     */
    private $middleWares;
    /**
     * Loaded route name.
     * such as '/filter'
     * @var string
     */
    private $loadingRouterName = '';

    /**
     * Connect constructor.
     * @param $res
     * @param $req
     */
    public function __construct($req, $res)
    {
        $this->middleWares = [];

        $this->req = $req;
        $this->res = $res;
    }

    /**
     * start the connect.
     *
     * @throws \Exception
     */
    public function start()
    {
        if (!empty($this->connectNext)) {
            throw new \Exception('Connect has been started!');
        }

        $this->connectNext = new ConnectNext(function () {
            if ($middleWare = current($this->middleWares)) {

                next($this->middleWares);
                if (is_array($middleWare)) {
                    list($filter, $instance) = $middleWare;
                    if ($this->routerMatch($filter)) {
                        //execute the router middleWare.
                        $instance($this->req, $this->res, $this->connectNext->next());
                    } else {
                        $this->next();
                    }
                } else {
                    //execute the initial middleWare.
                    $middleWare($this->req, $this->res, $this->connectNext->next());
                }
            } else {
                //catch 404.
                $this->res->render(new EchoEngine(), '404');
            }
        });

        $this->next();
    }

    /**
     * add middleWare.
     * $router = Application::router();
     * $router->on(Cookie::create());
     *
     * add router middleWare.
     * $router->on('/filter',function($req,$res,$next){}
     *
     * add router php fileName.
     * $router->on('user');//then the framework will find the /router/user.php,and load it.
     *
     * @param $filter
     * @param $object router ClassName or Instance.
     */
    public function on(...$args)
    {
        switch (count($args)) {
            case 1:
                $list = is_array($args[0]) ? $args[0] : [$args[0]];
                foreach ($list as $m) {
                    array_push($this->middleWares, $m);
                }
                break;
            case 2:
                if (is_string($args[1])) {
                    $this->analyzeAllRouters($args[0], $args[1]);
                } else {
                    array_push($this->middleWares, [$this->fillFilter($args[0]), $args[1]]);
                }
                break;
            default:
                throw new \Exception('middleWare append error');
                break;
        }
    }

    /**
     * remove all middleWare & routerMiddleWare.
     */
    public function gc()
    {
        foreach ($this->middleWares as $key => $item) {
            unset($this->middleWares[$key]);
        }
    }

    /**
     * find all router middleWares.
     */
    private function analyzeAllRouters($filter, $fileName)
    {
        if ($this->routerMatch($filter) && $fileName) {
            $this->loadingRouterName = $filter;
            Config::load($fileName);
            $this->loadingRouterName = '';
        }
    }

    /**
     * execute next middleWare.
     */
    private function next()
    {
        $nextFunction = $this->connectNext->next();
        $nextFunction();
    }

    /**
     * return the checked filter.
     * @param $filter
     * @return string
     */
    private function fillFilter($filter = '')
    {
        if (empty($filter)) {
            $filter = '/';
        } else if (substr($filter, 0, 1) != '/') {
            $filter = '/' . $filter;
        }
        if ($this->loadingRouterName && $this->loadingRouterName != '/') {
            $filter = substr($this->loadingRouterName, -1) == '/' ? substr($this->loadingRouterName, 1) . $filter : $this->loadingRouterName . $filter;
        }
        return $filter;
    }

    /**
     * filter router string & router-path.
     */
    private function routerMatch($filter)
    {
        /**
         * $path = '/dir/name';
         * $filter = '/dir';
         * $filter = '/dir/:name';
         * $path contains $filter
         */
        $path = $this->req->path;
        $this->req->route = $filter;
        if (strpos($path, $filter) === 0) {
            return true;
        } else if (strstr($filter, ':') == true) {
            //get the params from the path.
            if (strpos($path, explode(':', $filter)[0]) === 0) {
                $path_stack = explode('/', $path);
                $filter_stack = explode('/', $filter);
                $params = [];
                foreach ($filter_stack as $key => $value) {
                    if (strstr($value, ':')) {
                        $params[explode(':', $value)[1]] = $path_stack[$key];
                    }
                }
                $this->req->params = $params;
                return true;
            }
        } else {
            return false;
        }
    }
}

/**
 * Class ConnectNext.
 * In safe mode, the function is exposed
 *
 * @package cube\middleware
 */
class ConnectNext
{
    private $connect_array;

    public function __construct($next)
    {
        $this->connect_array = [$next];
    }

    public function next()
    {
        return $this->connect_array[0];
    }
}