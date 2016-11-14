<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/9/12
 * Time: 下午1:47
 */

namespace modules\session;

use cube\core\DynamicClass;
use cube\core\Config;

/**
 * Class Session.
 * Session中间件,也实现了ISession接口。
 * @package modules\session
 */
class Session
{
    public static function create()
    {
        return function ($req, $res, $next) {
            //default action.
            session_set_cookie_params(Config::get('core', 'session_timeout'));
            session_name(Config::get('core', 'session_name'));
            session_start();
            session_regenerate_id(true);

            $req->session(new LocalSession());

            $next();
        };
    }
}


/**
 * Class LocalSession.
 * @package modules\session
 */
class LocalSession extends DynamicClass
{
    public function __construct()
    {
    }

    public function __set($name,$value)
    {
        $_SESSION[$name] = $value;
    }

    public function __get($name)
    {
        return $_SESSION[$name];
    }

    public function getName()
    {
        // TODO: Implement setID() method.
        return session_name();
    }

    public function getID()
    {
        // TODO: Implement getID() method.
        return session_id();
    }

    public function delete($options)
    {
        // TODO: Implement delete() method.
        unset($_SESSION[$options]);
    }

    public function clear()
    {
        // TODO: Implement clear() method.
        session_destroy();
    }
}