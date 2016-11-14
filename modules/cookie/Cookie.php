<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/9/12
 * Time: 上午11:48
 */

namespace modules\cookie;

use cube\core\DynamicClass;

/**
 * class Cookie.
 * @package com\cube\core
 */
class Cookie
{
    public static function create()
    {
        return function ($req, $res, $next) {
            $req->cookie(new CookieInstance());
            $next();
        };
    }
}


/**
 * class Cookie.
 * @package com\cube\core
 */
class CookieInstance extends DynamicClass
{
    public function __construct()
    {
        $this->body = $_COOKIE;
    }

    public function __set($name, $value)
    {
        if (empty($value)) {
            setcookie($name, '', time() - 3600, '/');
        } else {
            setcookie($name, $value, null, '/');
        }
    }

    /**
     * set cookie & timeout.
     * @param $key
     * @param $value
     * @param $time
     * @return mixed
     */
    public function set($name, $value, $time = null)
    {
        setcookie($name, $value, $time, '/');
    }

    /**
     * 清除所有cookie.
     * @return mixed
     */
    public function clear()
    {
        foreach ($this->body as $name => $value) {
            $this->$name = null;
        }
    }
}