# orm
orm curd for the mysql

## cube-php/orm needs
* POD Ext.

## cube-php/framework contains the orm.
* <a href='https://github.com/cube-php/framework'>click me , and go to check the cube-php/framework</a>

## how to use the cube-orm?
```javascript
require __DIR__ . './orm/DB.php';

use orm\DB;


$config = [
    'host' => 'localhost',
    'port' => 3306,
    'user' => 'root',
    'password' => '',
    'db' => 'system',
    'prefix' => 'cube_orm_'
];

//init orm config.
DB::init($config);

DB::model('list')->where('a=1')->group('userid')->select();
```

## Class com\cube\db\DB
*  static init($options);// init db config
*  static model($tableName);//get the orm instance
*  static query($sql,$task = false);
*  static exec($sql,$task = true);

## Class com\cube\db\DBModel
*  __construct($tableName);
*  task(); // return the DBModel ,the sql will executed as the task when you use this
*  where($options);//return the DBModel
*  order($options);//return the DBModel
*  group($options);//return the DBModel
*  limit($start,$length);//return the DBModel
*  count();// return the result
*  sum($options);// return the result
*  select($options);// return the result
*  update($options);// return the result
*  delete($options);// return the result
*  insert($options);// return the result

## More demos.
*  where
```javascript
DB::model('list')->where('a=1 and (b=2 or c=3)')->select();
SQL:select * from list where a=1 and (b=2 or c=3);
Attension:where you c='string' , you should use where('c="string"');
```
*  order
```javascript
DB::model('list')->order('userid ASC')->select();
SQL:select * from list order by userid asc;

DB::model('list')->order(array('userid ASC','username DESC'))->select();
SQL:select * from list order by userid asc,username desc;
```
*  group
```javascript
DB::model('list')->group('userid')->select();
SQL:select * from list group by userid;

DB::model('list')->group(array('userid','username'))->select();
SQL:select * from list group by userid,username;
```
* limit
```javascript
DB::model('list')->limit(0,10)->select();
SQL:select * from list limit 0,10;
```
* count
```javascript
DB::model('list')->count();
SQL:select count(*) from list;

DB::model('list')->where('a=1')->count();
SQL:select count(*) from list where a=1;
```
* sum
```javascript
DB::model('list')->sum('score');
SQL:select sum(score) from list;

DB::model('list')->where('a=1')->sum('score');
SQL:select sum(score) from list where a=1;
```
* select
```javascript
DB::model('list')->select();
SQL:select * FROM list;

DB::model('list')->select('username');
SQL:select username from list;

DB::model('list')->select(array('username','team'));
SQL:select username,team from list;
```
* update
```javascript
DB::model('list')->where('a=1 and b="world"')->update(array('c'=>2,'d'=>'"hello"'));
SQL:update list c=2,d="hello" where a=1 and b="world";
```
* delete
```javascript
DB::model('list')->where('a=1')->delete();
SQL:delete from list where a=1;
```
* insert
```javascript
DB::model('list')->insert(array('a'=>1,'b'=>2));
SQL:insert into list (a,b) values (1,2);

DB::model('list')->where('a="hello"')->insert(array('a'=>1,'b'=>'2'));
SQL:INSERT INTO list (a,b) SELECT 1,2 FROM VISUAL WHERE NOT EXISTS (SELECT * FROM list WHERE name="hello");
```
