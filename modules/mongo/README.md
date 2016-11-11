# 该模块(插件)负责进行mongodb的快速连接和操作
* 该模块使用了扩展库mongo ,请确保框架所在服务器的php已经安装mongo ext并激活
* Mongo::connect(array('host'=>'localhost','port'=>27017,'db'=>'my','user'=>'admin','password'=>'123'));
* Mongo::model('collectionName')->find()->sort(array('name'=>-1))->skip(10)->limit(10);
* Mongo::close();