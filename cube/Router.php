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
     * parent router.
     * @var string
     */
    private static $parentRouter = null;
    /**
     * parent router filter.
     * @var string
     */
    private static $parentFilter = '';

    /**
     * create a router instance.
     * @param $req Request
     * @param $res Response
     * @return Router
     * @throws \Exception
     */
    public static function createFactory($req, $res)
    {
        if (!self::$parentRouter) {
            throw new \Exception('Router::createFactory no routerParent!');
        }
        $router = new Router($req, $res);
        self::$parentRouter->on(self::$parentFilter, $router);
        return $router;
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
     * parent router.
     * @var Router
     */
    private $parent = null;

    /**
     * get the test stack.
     * @return array
     */
    public function stack()
    {
        return $this->middleWares;
    }

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

        $this->parent = self::$parentRouter;
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
                    if ($this->routerMatch($filter)) {
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
                    array_push($this->middleWares, [$this->fillFilter($args[0]), $args[1]]);
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
        foreach ($this->middleWares as $key => $item) {
            if ($item) {
                if (is_array($item) && get_class($item) == 'Closure') {
                    $item->gc();
                }
            }
            unset($this->middleWares[$key]);
        }
    }

    /**
     * parse the php path name string to Router instance.
     * @param $filter string
     * @param $fileName string
     */
    private function pushAsRouter($filter, $fileName)
    {
        $filter = $this->fillFilter($filter);
        if ($this->routerMatch($filter) && $fileName) {
            self::$parentRouter = $this;
            self::$parentFilter = $filter;

            import($fileName);

            self::$parentRouter = null;
            self::$parentFilter = '';
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
     * fill the filter string.
     * such as
     * / => /
     * /user => /user/
     * user => /user/
     * user/a => /user/a/
     * /user/a => /user/a/
     *
     * @param $value string
     * @return string
     */
    private function fillFilter($value = '')
    {
        if (!$value) {
            $value = '/';
        } else if (substr($value, 0, 1) != '/') {
            $value = '/' . $value;
        }
        if (self::$parentFilter) {
            if (self::$parentFilter == '/') {
                $value = self::$parentFilter . $value;
            } else {
                $value = substr(self::$parentFilter, -1) == '/' ? (substr(self::$parentFilter, 0, -1) . $value) : (self::$parentFilter . $value);
            }
        }
        if ($value != '/' && substr($value, -1) != '/') {
            $value .= '/';
        }
        return $value;
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