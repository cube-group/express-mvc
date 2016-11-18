<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/9/13
 * Time: 下午12:20
 */

namespace redis;

use log\Log;
use utils\Utils;

if ($ext = Utils::is_miss_ext('redis')) {
    throw new \Exception('Ext ' . $ext . ' is not exist!');
}

/**
 * Class Redis.
 *
 * @package modules\redis
 */
class Redis
{
    /**
     * redis connect config.
     * @var
     */
    private static $options;
    /**
     * redis connect instance.
     * @var
     */
    private static $redis;

    /**
     * DataStore constructor.
     * array(
     *      'host'=>'localhost',
     *      'port'=>4396,
     *      'db'=>1,
     *      'password'=>''
     * );
     * @param $options
     */
    public static function init($options)
    {
        self::$options = $options;
    }

    /**
     * close the redis connection.
     *
     * @return bool
     */
    public static function close()
    {
        self::$redis = null;
        return true;
    }

    /**
     * get the redis db.
     *
     * Redis::model()->set('key','value');
     * Redis::model()->get('key');
     *
     * Redis::model()->setex('key',3600,'value');//1h TTL
     *
     * Redis::model()->setnx('key','value');//repeat-write
     *
     * Redis::model()->delete('key');
     * Redis::model()->delete(array('key1','key2','key3');
     *
     * Redis::model()->ttl('key');//get the life-cycle of the key
     *
     * Redis::model()->persist('key');//remove the key when its life-cycle is over,success return 1,failed return 0
     *
     * Redis::model()->mset(array('key1'=>'value1','key2'=>'value2'));
     *
     * Redis::model()->exists('key');//key is exist or not.
     *
     * Redis::model()->incr('key');//auto plus 1
     * Redis::model()->incrBy('key',10);//auto plus 10
     *
     * Redis::model()->decr('key');//Auto minus 1
     * Redis::model()->decrBy('key',10);//Auto minus 10
     */
    public static function model()
    {
        if (empty(self::$redis)) {
            $options = self::$options;
            try {
                self::$redis = new \Redis();
                self::$redis->connect($options["host"], $options["port"]);
                self::$redis->auth($options["password"]);
                self::$redis->select($options['db']);

                Log::log('Redis Connected host: ' . $options['host'] . ' port: ' . $options['port'] . ' db: ' . $options['db']);
            } catch (\RedisException $e) {
                return null;
                Log::log('Redis Error ' . $e->getTraceAsString());
            }
        }

        return self::$redis;
    }
}