<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/9/13
 * Time: 下午12:20
 */
namespace modules\mongo;

use com\cube\error\CubeException;
use com\cube\utils\SystemUtil;
use com\cube\log\Log;

//扩展检测.
if (SystemUtil::check_unknown_extension('mongo')) {
    throw new CubeException('Mongo Ext Error.', CubeException::$EXT_ERROR);
}

/**
 * Class MongoStore.
 * Mongodb快速连接类.
 * @package modules\mongo
 */
final class Mongo
{
    /**
     * 数据库参数暂存对象.
     * @var
     */
    private static $options;
    /**
     * Mongodb连接实例.
     * @var \MongoClient
     */
    private static $mongo;
    /**
     * DB 连接实例.
     * @var
     */
    private static $db;

    /**
     * DataStore constructor.
     * array(
     *      'host'=>'localhost',
     *      'port'=>27017,
     *      'db'=>'db',
     *      'user'=>'username',
     *      'password'=>'密码'
     * );
     * @param $options
     */
    public static function init($options)
    {
        self::$options = $options;
    }

    /**
     * 关闭MongoDB的所有连接.
     * @return bool
     */
    public static function close()
    {
        if (empty(self::$mongo)) {
            return true;
        }
        try {
            $connections = self::$mongo->getConnections();
            foreach ($connections as $con) {
                if ($con['connection']['connection_type_desc'] == "SECONDARY") {
                    self::$mongo->close($con['hash']);
                }
            }
            self::$db = null;
            self::$mongo = null;
            Log::log('MongoDB Closed');
            return true;
        } catch (\MongoException $e) {
            Log::log('MongoDB Close Error ' . $e->getTraceAsString());
        } catch (\Exception $e) {
            Log::log('MongoDB Close Error ' . $e->getTraceAsString());
        } catch (\ErrorException $e) {
            Log::log('MongoDB Close Error ' . $e->getTraceAsString());
        }
        return false;
    }

    /**
     * 获取collection引用.
     * Mongo::model('list')->find();
     * SQL:select * from list;
     * Mongo::model('list')->find(array('name'=>'hello'));
     * SQL:select * from list where name="hello";
     * Mongo::model('list')->find(array('name'=>'hello'),array('name','group'));
     * SQL:select name,group from list where name="hello";
     * Mongo::model('list')->find(array('$or'=>array('a'=>1,'b'=>2));
     * SQL:select * from list where (a=1 or b=2);
     * Mongo::model('list')->find(array('$and'=>array('a'=>1,'b'=>2));
     * SQL:select * from list where (a=1 and b=2);
     * Mongo::model('list')->find(array('$or'=>array('a'=>1,'b'=>2,'$and'=>array('c'=>3,'d'=>4)));
     * SQL:select * from list where (a=1 or b=2 or (c=3 and d=4));
     * Mongo::model('list')->find(array('$gt'=>array('c'=>4)));
     * SQL:select * from list where c>4;
     * Mongo::model('list')->find(array('$gte'=>array('c'=>4)));
     * SQL:select * from list where c>=4;
     * Mongo::model('list')->find(array('$lt'=>array('c'=>4)));
     * SQL:select * from list where c<4;
     * Mongo::model('list')->find(array('$lte'=>array('c'=>4)));
     * SQL:select * from list where c<=4;
     *
     * Mongo::model('list')->findOne(array('name'=>'hello'));
     * SQL:select * from list limit 0,1;
     *
     * Mongo::model('list')->find()->sort(array('name'=>1));
     * SQL:select * from list order by name asc;
     * Mongo::model('list')->find()->sort(array('name'=>-1));
     * SQL:select * from list order by name desc;
     *
     * Mongo::model('list')->find()->skip(0)->limit(10);
     * SQL:select * from list limit 0,10;
     *
     * Mongo::model('list')->update(array('name'=>'hello'),array('$set'=>array('a'=>1,'b'=>2));
     * SQL:update list a=1,b=2 where name="hello";
     *
     * Mongo::model('list')->update(array('name'=>'hello'),array('$inc'=>array('a'=>1));
     * SQL:update list a=a+1 where name="hello";
     *
     * ...$筛选器,$or/$and/$gt/$gte/$lt/$lte...
     * ...更多$修改器百度去吧,$set/$inc/$unset/$push/$pop/$upsert...
     *
     * Mongo::model('list')->remove({'name'=>'hello'});
     * SQL:delete from list where name="hello";
     *
     * Mongo::model('list')->insert({'name'=>'hello'});
     * SQL:insert into list name values 'hello';
     *
     * Mongo::model('list')->insert({'name'=>'hello'});
     * SQL:insert into list name values 'hello';
     *
     * Mongo::model('list')->save({'name'=>'hello'});
     * SQL:INSERT INTO list (name) SELECT ('hello') FROM VISUAL WHERE NOT EXISTS (SELECT * FROM list WHERE name="hello");
     *
     *
     * @param $collection_name
     * @return mixed
     */
    public static function model($collection_name)
    {
        if (empty(self::$mongo)) {
            $options = self::$options;
            try {
                self::$mongo = new \MongoClient('mongodb://'.$options['host'] . ':' . $options['port'],
                array('username'=>$options['user'],'password'=>$options['password'],'db'=>$options['db'])
                );

                Log::log('Mongo Connected host: ' . $options['host'] . ' port: ' . $options['port'] . ' db: ' . $options['db']);
            } catch (\MongoConnectionException $e) {
                Log::log('Mongo Error ' . $e->getTraceAsString());
                return null;
            }
        }

        return self::$mongo->$options['db']->$collection_name;
    }
}
