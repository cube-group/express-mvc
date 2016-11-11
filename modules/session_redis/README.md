# 该模块(插件)主要是用于代理Application->Request->session
* 该module使用了redis存储session_id()的方式,代替了PHP默认的session文件存储大大提高了效率和安全性
* $app->$req->session->getName();//获取当前session的name,即cookie中用于记录用户唯一标识的key
* $app->$req->session->getID();//获取当前session的id,即cookie中用于记录用户唯一标识的value
* $app->$req->session->username = 'hello';//设置session
* $app->$req->session->username;//获取session中的username
* session的统一过期时间请在/package.json中的json.framework.session_timeout设置
* $qpp->$req->session->delete('key');//删除session中的key
* $qpp->$req->session->clear();//清除跟session->getID()相关联的所有key