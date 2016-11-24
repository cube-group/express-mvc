<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/8/26
 * Time: 上午9:59
 */

namespace cube;

use engine\EchoEngine;

/**
 * Class Connect.
 * MiddleWare Controller.
 * @package com\cube\middleware
 */
final class Router
{
    /**
     * global parent router.
     * @var string
     */
    private static $globalParentRouter = null;
    /**
     * child router filter.
     * @var string
     */
    private static $globalFilter = '';

    /**
     * create a router instance.
     * @param $req Request
     * @param $res Response
     * @return Router
     * @throws \Exception
     */
    public static function createFactory($req, $res)
    {
        if (!self::$globalParentRouter) {
            throw new \Exception('Router::createFactory no globalParentRouter!');
        }
        $router = new Router($req, $res, self::$globalFilter);
        self::$globalParentRouter->on(self::$globalFilter, $router);
        return $router;
    }

    /**
     * filter the filter string.
     * @param $filter string
     * @return string
     */
    private static function getFilter($filter)
    {
        if (!$filter || $filter == '/') {
            return '/';
        }
        if (substr($filter, 0, 1) != '/') {
            $filter = '/' . $filter;
        }
        if (substr($filter, -1) != '/') {
            $filter .= '/';
        }
        return $filter;
    }

    /**
     * fill the filter string.
     * $routerFilter: /user/
     * $filter: /login
     * absoluteFilter: /user/login/
     *
     * @param $filter string
     * @param $routerFilter string
     * @return string
     */
    private static function getAbsoluteFilter($filter, $routerFilter)
    {
        return substr($routerFilter, 0, -1) . self::getFilter($filter);
    }


    /**
     * request instance reference.
     * @var Request
     */
    private $req;
    /**
     * response instance reference.
     * @var Response
     */
    private $res;
    /**
     * next function instance.
     * @var Connect
     */
    private $connect;
    /**
     * middleWare stack.
     * @var array
     */
    private $middleWares;
    /**
     * parent router (for the multiple mode).
     * @var Router
     */
    private $parent = null;
    /**
     * current router filter (for the multiple mode).
     * @var string
     */
    private $filter = '';

    /**
     * Connect constructor.
     * @param $req Request
     * @param $res Response
     * @param $filter string
     */
    public function __construct($req, $res, $filter = '/')
    {
        $this->middleWares = [];

        $this->req = $req;
        $this->res = $res;

        $this->parent = self::$globalParentRouter;
        $this->filter = $this->parent ? self::getAbsoluteFilter($filter, $this->parent->filter()) : $filter;
    }

    /**
     * start the connect.
     *
     * @throws \Exception
     */
    public function start()
    {
        if ($this->connect) {
            throw new \Exception('Connect has been started!');
        }

        $this->connect = new Connect(function () {
            if ($middleWare = current($this->middleWares)) {

                next($this->middleWares);

                if (is_array($middleWare)) {
                    list($filter, $instance) = $middleWare;
                    if (!is_string($instance) && $this->routerMatch($filter)) {
                        //execute the router middleWare.
                        if (get_class($instance) == 'Closure') {
                            $instance($this->req, $this->res, $this->connect->next());
                        } else {
                            $instance->start();
                        }
                    } else {
                        $this->next();
                    }
                } else {
                    //execute the initial middleWare.
                    $middleWare($this->req, $this->res, $this->connect->next());
                }
            } else {
                //catch 404.
                if ($this->parent) {
                    $this->parent->next();
                } else {
                    $this->res->render(new EchoEngine(), '404');
                }
            }
        });

        $this->next();
    }

    /**
     * add middleWare.
     * $router = Cube::router();
     * $router->on(Cookie::create());
     *
     * add router middleWare.
     * $router->on('/filter',function($req,$res,$next){}
     *
     * add router php fileName.
     * $router->on('/user,'router/user');//then the framework will find the /router/user.php,and load it.
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
                    $this->pushAsRouter($args[0], $args[1]);
                } else {
                    array_push($this->middleWares, [self::getFilter($args[0]), $args[1]]);
                }
                break;
            default:
                throw new \Exception('middleWare add error');
                break;
        }
    }

    /**
     * remove all middleWare & routerMiddleWare.
     */
    public function gc()
    {
        $this->req = null;
        $this->res = null;
        $this->parent = null;
        $this->filter = '';
        foreach ($this->middleWares as $key => $item) {
            if ($item && is_array($item) && get_class($item[1]) == '\cube\Router') {
                $item->gc();
            }
            unset($this->middleWares[$key]);
        }
    }

    /**
     * get the current short filter.
     * @return string
     */
    public function filter()
    {
        return $this->filter;
    }

    /**
     * get the test stack.
     * @return array
     */
    public function stack()
    {
        return $this->middleWares;
    }


    /**
     * parse the php path name string to Router instance.
     * @param $filter string
     * @param $fileName string
     */
    private function pushAsRouter($filter, $fileName)
    {
        $absoluteFilter = self::getAbsoluteFilter($filter, $this->filter);
        if ($fileName && $this->routerMatch($absoluteFilter)) {
            self::$globalParentRouter = $this;
            self::$globalFilter = $filter;

            import($fileName);

            self::$globalParentRouter = null;
            self::$globalFilter = '';
        } else {
            array_push($this->middleWares, [self::getFilter($filter), $fileName]);
        }
    }

    /**
     * execute next middleWare.
     * @return void
     */
    private function next()
    {
        $nextFunction = $this->connect->next();
        $nextFunction();
    }

    /**
     * filter router string & router-path.
     *
     * demo: match
     * path: /user/
     * filter: /
     *
     * demo: not match
     * path: /user/
     * filter : /u
     *
     * demo: not match
     * path: /user or /user/ or /user/a/b
     * filter: /user/:id
     *
     * demo: match
     * path: /user/a
     * filter: /user/:id
     *
     * demo: match
     * path: /user/a/b
     * filter: /user/:id/:name
     *
     * demo: not match
     * path: /user/a/
     * filter: /user/:id/:name
     *
     * @param $filter string
     */
    private function routerMatch($filter)
    {
        $this->req->route = $filter;

        $path = $this->req->path;
        if (strpos($path, $filter) === 0) {
            return true;
        } else if (strstr($filter, ':') == true) {
            //get the params from the path.
            $path_stack = explode('/', $path);
            $filter_stack = explode('/', $filter);
            //use strict.
            if (count($path_stack) == count($filter_stack)) {
                $params = [];
                foreach ($filter_stack as $key => $value) {
                    if ($value != '' && strstr($value, ':') == true) {
                        $params[explode(':', $value)[1]] = $path_stack[$key];
                    } else if ($value != $path_stack[$key]) {
                        //length equal but other value not equal
                        return false;
                    }
                }
                $this->req->params = $params;
                return true;
            }
        }
        return false;
    }
}

/**
 * Class Connect.
 * In safe mode, the function is exposed
 *
 * @package cube\middleware
 */
class Connect
{
    private $body;

    /**
     * Connect constructor.
     * @param $next \Closure
     */
    public function __construct($next)
    {
        $this->body = [$next];
    }

    /**
     * return the next function.
     * @return \Closure
     */
    public function next()
    {
        return $this->body[0];
    }
}