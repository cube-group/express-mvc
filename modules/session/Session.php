<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/9/12
 * Time: 下午1:47
 */

namespace modules\session;

use com\cube\core\BaseDynamic;
use com\cube\core\Config;
use com\cube\core\ISession;
use com\cube\core\Response;
use com\cube\core\Request;
use com\cube\middleware\MiddleWare;

/**
 * Class Session.
 * Session中间件,也实现了ISession接口。
 * @package modules\session
 */
class Session extends MiddleWare
{
    public function run(Request $req, Response $res)
    {
        //default action.
        session_set_cookie_params(Config::get('core', 'session_timeout'));
        session_name(Config::get('core', 'session_name'));
        session_start();

        session_regenerate_id(true);

        $req->session(new LocalSession());
    }
}


/**
 * Class LocalSession.
 * @package modules\session
 */
class LocalSession extends BaseDynamic implements ISession
{
    public function __construct()
    {
        $this->body = $_SESSION;
    }

    public function __set($name,$value)
    {
        $_SESSION[$name] = $value;
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
        parent::delete($options);
        unset($_SESSION[$options]);
    }

    public function clear()
    {
        // TODO: Implement clear() method.
        parent::clear();
        session_destroy();
    }
}