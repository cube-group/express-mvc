<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/9/19
 * Time: 下午7:02
 */

namespace modules\session_redis;

use com\cube\core\BaseDynamic;
use com\cube\core\ISession;
use com\cube\log\Log;
use modules\redis\Redis;

/**
 * Class RedisSessionInstance.
 * 远程session存储.
 * @package modules\session
 */
class RedisSessionInstance extends BaseDynamic implements ISession
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
            Log::log('RedisSession Set Error '.$e->getTraceAsString());
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
     * 关闭redis连接.
     */
    public function close()
    {
        Redis::close();
        self::$options = null;
    }
}