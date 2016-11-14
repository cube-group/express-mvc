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
     * Connect constructor.
     * @param $res
     * @param $req
     * @param $modules initial modules.
     */
    public function __construct($req, $res, $modules = null)
    {
        $this->middleWares = [];

        $this->req = $req;
        $this->res = $res;

        if (!empty($modules)) {
            $this->on($modules);
        }
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
                    $filter = $middleWare['filter'];
                    $instance = $middleWare['instance'];
                    if ($this->routerMatch($filter)) {
                        //execute the router middleWare.
                        $instance($this->req, $this->res, $this->connectNext->next());
                    }else{
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
     * add router middleware className String.
     * @param $filter
     * @param $object router ClassName or Instance.
     */
    public function on(...$args)
    {
        //(args length == 1 && is_array) = MiddleWare List
        //(args length == 1 && !is_array) = MiddleWare Instance
        //(args length == 2) == RouterMiddleWare function
        switch (count($args)) {
            case 1:
                $list = is_array($args[0]) ? $args[0] : [$args[0]];
                foreach ($list as $m) {
                    array_push($this->middleWares, $m);
                }
                break;
            case 2:
                array_push($this->middleWares, ['filter' => $this->fillFilter($args[0]), 'instance' => $args[1]]);
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
        return $filter;
    }

    /**
     * filter router string & router-path.
     */
    private function routerMatch($filter)
    {
        $path = $this->req->path;

        /**
         * $path = '/dir/name';
         * $filter = '/dir';
         * $filter = '/dir/:name';
         * $path contains $filter
         */
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
                $this->req->route = $filter;
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