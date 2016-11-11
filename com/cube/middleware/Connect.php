<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/8/26
 * Time: 上午9:59
 */

namespace com\cube\middleware;

use com\cube\core\Application;


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
    private $request;
    /**
     * response instance reference.
     * @var
     */
    private $response;
    /**
     * application instance reference.
     * @var
     */
    private $app;
    /**
     * middleWare stack.
     * @var array
     */
    private $middleWares;
    /**
     * router middleWare stack.
     * @var array
     */
    private $routerMiddleWares;

    /**
     * Connect constructor.
     * @param $res
     * @param $req
     * @param $app
     */
    public function __construct($res, $req, $app)
    {
        $this->middleWares = array();
        $this->routerMiddleWares = array();

        $this->request = $res;
        $this->response = $req;
        $this->app = $app;
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
                    $m->run($this->request, $this->response);
                }
                break;
            case 2:
                if (empty($args[0])) {
                    $args[0] = '/';
                } else if (substr($args[0], 0, 1) != '/') {
                    throw new \Exception('middleWare filter error');
                }
                array_push($this->routerMiddleWares, ['filter' => strtolower($args[0]), 'middleWare' => $args[1]]);
                break;
            default:
                throw new \Exception('middleWare append error');
                break;
        }
    }

    /**
     * reset the routerMiddleWare stack.
     */
    public function restart()
    {
        reset($this->routerMiddleWares);
        $this->next();
    }

    /**
     * run the current routerMiddleWare.
     */
    public function next()
    {
        $middleWare = current($this->routerMiddleWares);

        //end check or 404.
        if (empty($middleWare)) {
            $this->app->onCatch404();
            return;
        }

        next($this->routerMiddleWares);

        //router middleware.
        $filter = $middleWare['filter'];
        $instance = $middleWare['middleWare'];
        if ($this->routerMatch($filter)) {
            $instance($this->request, $this->response, $this);
        } else {
            $this->next();
        }

    }

    /**
     * remove all middleWare & routerMiddleWare.
     */
    public function gc()
    {
        foreach ($this->middleWares as $key1 => $middleWare) {
            $middleWare->end();
            unset($this->middleWares[$key1]);
        }
        foreach ($this->routerMiddleWares as $key2 => $routerMiddleWare) {
            unset($this->routerMiddleWares[$key2]);
        }
    }

    /**
     * filter router string & router-path.
     */
    private function routerMatch($filter)
    {
        $path = $this->request->path;

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
                Application::getInstance()->request->params = $params;
                Application::getInstance()->request->route = $filter;
                return true;
            }
        } else {
            return false;
        }
    }
}