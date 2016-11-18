<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/9/12
 * Time: 下午1:47
 */

namespace session;
use redis\Redis;
use utils\DynamicClass;

/**
 * Class RedisSession.
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

            $session_name = constant('CONFIG')['core']['session_name'];
            $session_timeout = constant('CONFIG')['core']['session_timeout'];
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


/**
 * Class RedisSessionInstance.
 *
 * @package modules\session
 */
class RedisSessionInstance extends DynamicClass
{
    private static $options;

    /**
     * RemoteSession constructor.
     * session_id.
     * session_name.
     * session_timeout.
     * @param $options
     */
    public function __construct($options)
    {
        self::$options = $options;

        Redis::init(self::$options);
    }

    public function __set($name, $value)
    {
        // TODO: Implement set() method.
        try {
            Redis::model()->hset(self::$options['session_id'], $name, $value);
            return true;
        } catch (\RedisException $e) {
            Log::log('RedisSession Set Error ' . $e->getTraceAsString());
            return false;
        }
    }

    public function __get($name)
    {
        // TODO: Implement get() method.
        try {
            return Redis::model()->hget(self::$options['session_id'], $name);
        } catch (\RedisException $e) {
            return '';
        }
    }

    public function getName()
    {
        // TODO: Implement setID() method.
        return self::$options['session_name'];
    }

    public function getID()
    {
        // TODO: Implement getID() method.
        return self::$options['session_id'];
    }

    public function delete($options)
    {
        // TODO: Implement delete() method.
        parent::delete($options);
        try {
            Redis::model()->hdel(self::$options['session_id'], $options);
        } catch (\RedisException $e) {
            return null;
        }
    }

    public function clear()
    {
        // TODO: Implement clear() method.
        parent::clear();
        try {
            Redis::model()->del(self::$options['session_id']);
        } catch (\RedisException $e) {
            return null;
        }
    }

    /**
     * close the redis connection.
     */
    public function close()
    {
        Redis::close();
        self::$options = null;
    }
}