<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/9/13
 * Time: 下午12:20
 */
namespace mongo;

use log\Log;
use utils\Utils;

if ($ext = Utils::is_miss_ext('mongo')) {
    throw new \Exception('Ext ' . $ext . ' is not exist!');
}

/**
 * Class MongoStore.
 *
 * @package modules\mongo
 */
final class Mongo
{
    /**
     * mongo config.
     * @var
     */
    private static $options;
    /**
     * mongo connect instance.
     * @var \MongoClient
     */
    private static $mongo;
    /**
     * mongo database connect instance.
     * @var
     */
    private static $db;

    /**
     * DataStore constructor.
     * array(
     *      'host'=>'localhost',
     *      'port'=>27017,
     *      'db'=>'db',
     *      'username'=>'username',
     *      'password'=>'密码'
     * );
     * @param $options
     */
    public static function init($options)
    {
        self::$options = $options;
    }

    /**
     * close the mongodb connection.
     *
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
     * get the mongo collection.
     *
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
     * ...$filter: $or/$and/$gt/$gte/$lt/$lte...
     * ...more $ you need to google...$set/$inc/$unset/$push/$pop/$upsert...
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
                self::$mongo = new \MongoClient('mongodb://' . $options['host'] . ':' . $options['port'],
                    array('username' => $options['username'], 'password' => $options['password'], 'db' => $options['db'])
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
