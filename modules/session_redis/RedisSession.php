<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/9/12
 * Time: 下午1:47
 */

namespace modules\session_redis;

use cube\core\Config;

/**
 * Class RedisSession.
 * Session中间件,也实现了ISession接口。
 * @package modules\session
 */
class RedisSession
{
    /**
     * Session constructor.
     * @param $options
     */
    public static function create($options)
    {
        return function ($req, $res, $next) {
            Config::load("modules/session_redis/RedisSessionInstance.php");

            $session_name = Config::get('core', 'session_name');
            $session_timeout = Config::get('core', 'session_timeout');
            $session_id = $req->cookie->$session_name;

            if (empty($session_id)) {
                $session_id = uniqid('cube_');
                $req->cookie->set($session_name, $session_id, time() + $session_timeout);
            }

            $options['session_id'] = $session_id;
            $options['session_name'] = $session_name;

            $instance = new RedisSessionInstance($options);
            $req->session($instance);

            //continue to execute.
            $next();

            //gc
            $instance->close();
        };
    }
}