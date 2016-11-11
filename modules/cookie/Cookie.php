<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/9/12
 * Time: 上午11:48
 */

namespace modules\cookie;

use com\cube\core\BaseDynamic;
use com\cube\core\Response;
use com\cube\core\Request;
use com\cube\middleware\MiddleWare;

/**
 * class Cookie.
 * @package com\cube\core
 */
class Cookie extends MiddleWare
{
    public function run(Request $req, Response $res)
    {
        $req->cookie(new CookieInstance());
    }
}


/**
 * class Cookie.
 * @package com\cube\core
 */
class CookieInstance extends BaseDynamic
{
    public function __construct()
    {
        $this->body = $_COOKIE;
    }

    public function __set($name, $value)
    {
        setcookie($name, $value, null, '/');
    }

    /**
     * 设置cookie.
     * @param $key
     * @param $value
     * @param $time
     * @return mixed
     */
    public function set($key, $value, $time = null)
    {
        setcookie($key, $value, $time, '/');
    }

    /**
     * 删除某一个cookie.
     * @param $key
     * @return mixed
     */
    public function delete($key)
    {
        setcookie($key, '', time() - 3600, '/');
    }

    /**
     * 清除所有cookie.
     * @return mixed
     */
    public function clear()
    {
        foreach ($this->body as $key => $value) {
            $this->delete($key);
        }
    }
}