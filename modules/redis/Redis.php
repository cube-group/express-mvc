<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/9/13
 * Time: 下午12:20
 */

namespace modules\redis;

use com\cube\error\CubeException;
use com\cube\log\Log;
use com\cube\utils\SystemUtil;

//扩展检测.
if (SystemUtil::check_unknown_extension('redis')) {
    throw new CubeException('Redis Ext Error.', CubeException::$EXT_ERROR);
}

/**
 * Class Redis.
 * 暂未实现.
 * Redis数据库快速连接类.
 * @package modules\redis
 */
class Redis
{
    /**
     * 数据库参数暂存对象.
     * @var
     */
    private static $options;
    /**
     * redis连接实例.
     * @var
     */
    private static $redis;

    /**
     * DataStore constructor.
     * array(
     *      'host'=>'127.0.0.1',
     *      'port'=>6379,
     *      'db'=>'1',
     *      'password'=>'密码',
     *      'timeout'=>30
     * );
     * @param $options
     */
    public static function init($options)
    {
        self::$options = $options;
    }

    /**
     * 关闭redis服务.
     * @return bool
     */
    public static function close()
    {
        self::$redis = null;
        return true;
    }

    /**
     * 获得redis数据库引用.
     * Redis::connect(array('127.0.0.1',6379);
     *
     * Redis::model()->set('key','value');
     * Redis::model()->get('key');
     *
     * Redis::model()->setex('key',3600,'value');//1h TTL
     *
     * Redis::model()->setnx('key','value');//可以重复写入多条
     *
     * Redis::model()->delete('key');
     * Redis::model()->delete(array('key1','key2','key3');
     *
     * Redis::model()->ttl('key');//得到一个key的生存时间
     *
     * Redis::model()->persist('key');//返回bool 移除生存时间到期的key如果key到期返回true ,否则返回false
     *
     * Redis::model()->mset(array('key1'=>'value1','key2'=>'value2'));
     *
     * Redis::model()->exists('key');//判断key是否存在
     *
     * Redis::model()->incr('key');//自动加1
     * Redis::model()->incrBy('key',10);//自动加10
     *
     * Redis::model()->decr('key');//自动减1
     * Redis::model()->decrBy('key',10);//自动减10
     */
    public static function model()
    {
        if (empty(self::$redis)) {
            $options = self::$options;
            try {
                self::$redis = new \Redis();
                self::$redis->connect($options["host"], $options["port"]);//, $options["timeout"]);
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