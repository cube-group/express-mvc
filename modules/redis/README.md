# 该模块(插件)负责进行redis的快速连接和操作
* 该模块使用了扩展库redis ,请确保框架所在服务器的php已经安装redis ext并激活
* Redis::connect(array('host'=>'localhost','port'=>27017,'db'=>'1','password'=>'123'));
* Redis::model()->set('key','value');
* Redis::model()->get('key');
* Redis::model()->hset('key','hm_key','value');
* Redis::model()->hget('key','hm_key');
* Redis::model()->hgetall('key','hm_key');
* Redis::model()->hmset('key','hm_key1','value1','hm_key2','value2');
* Redis::model()->hmget('key','hm_key1','hm_key2');
* Redis::close();