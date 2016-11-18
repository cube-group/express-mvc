### Redis Fast Connection.
* Redis set the init config array.
(Redis::init is not to create a connection, but simply to accept the configuration file.)
```javascript
use redis\Redis;
Redis::init([
    'host'=>'lcoalhost',
    'port'=>4396,
    'db'=>1,
    'password'=>''
]);
```
* Redis use.
```javascript
Redis::model()->set('key','value');
Redis::model()->get('key');

Redis::model()->setex('key',3600,'value');//1h TTL

Redis::model()->setnx('key','value');//repeat-write

Redis::model()->delete('key');
Redis::model()->delete(array('key1','key2','key3');

Redis::model()->ttl('key');//get the life-cycle of the key

Redis::model()->persist('key');//remove the key when its life-cycle is over,success return 1,failed return 0

Redis::model()->mset(array('key1'=>'value1','key2'=>'value2'));

Redis::model()->exists('key');//key is exist or not.

Redis::model()->incr('key');//auto plus 1
Redis::model()->incrBy('key',10);//auto plus 10

Redis::model()->decr('key');//Auto minus 1
Redis::model()->decrBy('key',10);//Auto minus 10
```