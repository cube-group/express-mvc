### Mongo Fast Connection.
* Mongo set the init config array.
(Mongo::init is not to create a connection, but simply to accept the configuration file.)
```javascript
use mongo\Mongo;
Mongo::init([
    'host'=>'lcoalhost',
    'port'=>27017,
    'db'=>'system',
    'username'=>'root',
    'password'=>''
]);
```
* Mongo use.
```javascript
Mongo::model('list')->find();
SQL:select * from list;
Mongo::model('list')->find(array('name'=>'hello'));
SQL:select * from list where name="hello";
Mongo::model('list')->find(array('name'=>'hello'),array('name','group'));
SQL:select name,group from list where name="hello";
Mongo::model('list')->find(array('$or'=>array('a'=>1,'b'=>2));
SQL:select * from list where (a=1 or b=2);
Mongo::model('list')->find(array('$and'=>array('a'=>1,'b'=>2));
SQL:select * from list where (a=1 and b=2);
Mongo::model('list')->find(array('$or'=>array('a'=>1,'b'=>2,'$and'=>array('c'=>3,'d'=>4)));
SQL:select * from list where (a=1 or b=2 or (c=3 and d=4));
Mongo::model('list')->find(array('$gt'=>array('c'=>4)));
SQL:select * from list where c>4;
Mongo::model('list')->find(array('$gte'=>array('c'=>4)));
SQL:select * from list where c>=4;
Mongo::model('list')->find(array('$lt'=>array('c'=>4)));
SQL:select * from list where c<4;
Mongo::model('list')->find(array('$lte'=>array('c'=>4)));
SQL:select * from list where c<=4;

Mongo::model('list')->findOne(array('name'=>'hello'));
SQL:select * from list limit 0,1;

Mongo::model('list')->find()->sort(array('name'=>1));
SQL:select * from list order by name asc;
Mongo::model('list')->find()->sort(array('name'=>-1));
SQL:select * from list order by name desc;

Mongo::model('list')->find()->skip(0)->limit(10);
SQL:select * from list limit 0,10;

Mongo::model('list')->update(array('name'=>'hello'),array('$set'=>array('a'=>1,'b'=>2));
SQL:update list a=1,b=2 where name="hello";

Mongo::model('list')->update(array('name'=>'hello'),array('$inc'=>array('a'=>1));
SQL:update list a=a+1 where name="hello";

...$filter: $or/$and/$gt/$gte/$lt/$lte...
...more $ you need to google...$set/$inc/$unset/$push/$pop/$upsert...

Mongo::model('list')->remove({'name'=>'hello'});
SQL:delete from list where name="hello";

Mongo::model('list')->insert({'name'=>'hello'});
SQL:insert into list name values 'hello';

Mongo::model('list')->insert({'name'=>'hello'});
SQL:insert into list name values 'hello';

Mongo::model('list')->save({'name'=>'hello'});
SQL:INSERT INTO list (name) SELECT ('hello') FROM VISUAL WHERE NOT EXISTS (SELECT * FROM list WHERE name="hello");
```