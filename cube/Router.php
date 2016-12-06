<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/8/26
 * Time: 上午9:59
 */

namespace cube;

use cube\engine\EchoEngine;
use utils\Utils;

/**
 * Class Connect.
 * MiddleWare Controller.
 * Copyright(c) 2016 Linyang.
 * MIT Licensed
 * @package cube
 */
final class Router
{
    /**
     * global parent router.
     * @var Router
     */
    private static $tempParentRouterInfo = null;


    /**
     * create a router instance.
     * @param $req Request
     * @param $res Response
     * @return Router
     * @throws \Exception
     */
    public static function createFactory($req, $res)
    {
        if (!self::$tempParentRouterInfo) {
            throw new \Exception('Router::createFactory no globalParentRouter or globalMiddleWare!');
        }

        list($router, $stack) = self::$tempParentRouterInfo;
        list($filter, $fileName) = $stack->current();
        $router = new Router($req, $res, $filter);
        $stack->current([$filter, $router, $fileName]);

        return $router;
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
    private static function match($absoluteFilter, $req)
    {
//        echo 'match: ' . $absoluteFilter . '  ' . $req->path . '<br>';

        if (strpos($req->path, $absoluteFilter) === 0) {
            $req->route = $absoluteFilter;
            return true;
        } else if (strstr($absoluteFilter, ':') == true) {
            //get the params from the path.
            $path_stack = explode('/', $req->path);
            $filter_stack = explode('/', $absoluteFilter);
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
                $req->params($params);
                $req->route = $absoluteFilter;
                return true;
            }
        }
        return false;
    }


    /**
     * fill the filter string.
     *
     * $routerFilter + $filter => $absoluteFilter
     * '/user/' + '/login' => '/user/login/'
     *
     * @param $filter string
     * @param $routerFilter string
     * @return string
     */
    private static function getAbsoluteFilter($filter, $routerFilter)
    {
        return substr($routerFilter, 0, -1) . Utils::pathFilter($filter);
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
     * @var \Closure
     */
    private $connect;
    /**
     * middleWare stack.
     * @var MiddlewareArray
     */
    private $stack;
    /**
     * parent router.
     * @var Router
     */
    private $parent = null;
    /**
     * current router filter.
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
        $this->stack = new MiddlewareArray();

        $this->req = $req;
        $this->res = $res;

        $this->parent = self::$tempParentRouterInfo[0];
        $this->filter = $this->parent ? self::getAbsoluteFilter($filter, $this->parent->filter()) : $filter;
    }


    /**
     * add middleWare.
     *
     * support php5.4...
     *
     * add router middleWare.
     * $router->on(['/filter',function($req,$res,$next){}
     *
     * add router php fileName.
     * $router->on('/user,'router/user');//then the framework will find the /router/user.php,and load it.
     *
     * @param $arg1 string|\Closure
     * @param $arg2 string|\Closure|Router
     * @param $object router ClassName or Instance.
     */
    public function on($arg1, $arg2 = null)
    {
        if ($arg2) {
            $this->stack->push([Utils::pathFilter($arg1), $arg2]);
        } else {
            $list = is_array($arg1) ? $arg1 : [$arg1];
            foreach ($list as $m) {
                $this->stack->push($m);
            }
        }
    }

    /**
     * remove all middleWare & routerMiddleWare.
     */
    public function gc()
    {
        foreach ($this->stack->value as $key => $item) {
            if ($item && is_array($item) && get_class($item[1]) == 'cube\Router') {
                $item[1]->gc();
            }
            $this->stack->del($key);
        }

        $this->req = null;
        $this->res = null;
        $this->parent = null;
        $this->filter = '';
        $this->stack = null;
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
        return $this->stack->value;
    }


    /**
     * start the connect.
     *
     * @param $reset boolean
     * @throws \Exception
     */
    public function next($reset = false)
    {
        if (!$this->connect) {
            $this->connect = function () {
                if ($item = $this->stack->current()) {
                    $this->execMiddleWare($item);
                } else {
                    if ($this->parent) {
                        $this->parent->next();
                    } else { //catch 404.
                        $this->res->render('404');
                    }
                }
            };
        }

        if (!$this->stack) {
            throw new \Exception('App can not start!');
        }

        if ($reset) {
            $this->stack->reset();
        }

        $nextFunction = $this->connect;
        $nextFunction();
    }


    /**
     * execute the middleWare.
     *
     * @param $middleWare array|\Closure
     */
    private function execMiddleWare($middleware)
    {
        $this->stack->next();

        if (is_array($middleware)) {
            list($filter, $obj) = $middleware;
            $absoluteFilter = self::getAbsoluteFilter($filter, $this->filter);

            if (self::match($absoluteFilter, $this->req)) {
                if (is_string($obj)) {
                    //( $filter , $fileName )
                    $this->pushAsRouter($filter, $obj);
                } else if (get_class($obj) == 'Closure') {
                    //( $filter , function($req,$res,$next) )
                    $obj($this->req, $this->res, $this->connect);
                } else {
                    //( $filter , Router )
                    $obj->next();
                }
            } else {
                $this->next();
            }

        } else if (!$this->req->redirected && get_class($middleware) == 'Closure') {
            //( function($req,$res,$next) )
            $middleware($this->req, $this->res, $this->connect);
        } else {
            $this->next();
        }
    }


    /**
     * parse the php path name string to Router instance.
     * @param $middleware array
     */
    private function pushAsRouter($filter, $fileName)
    {
        //move the index prev.
        $this->stack->prev();

        self::$tempParentRouterInfo = [$this, $this->stack];

        import($fileName);

        //clear temp info.
        self::$tempParentRouterInfo = null;

        //execute the prev middleWare.
        $this->stack->execNext();

        $this->stack->next();
    }
}


/**
 * Class MiddlewareArray.
 * Package Array.
 * @package cube
 */
class MiddlewareArray
{
    private $value = [];
    private $index = 0;

    public function __get($name)
    {
        // TODO: Implement __get() method.
        return $this->$name;
    }

    public function push($value)
    {
        array_push($this->value, $value);
    }

    public function del($i)
    {
        unset($this->value[$i]);
    }

    public function current($value = null)
    {
        if ($value) {
            $this->value[$this->index] = $value;
        } else {
            return ($this->index < $this->length()) ? $this->value[$this->index] : false;
        }
    }

    public function execNext()
    {
        $this->value[$this->index][1]->next();
    }

    public function next()
    {
        $this->index++;
    }

    public function prev()
    {
        $this->index--;
    }

    public function reset()
    {
        $this->index = 0;
    }

    public function length()
    {
        return count($this->value);
    }
}