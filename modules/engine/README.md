# 该模块(插件)主要为Application->Response->render的页面代码渲染器引擎
* 框架内部实现方式: $app->$res->render(ViewEngine $engine,$name,$data=null);
* EchoEngine 继承自 ViewEngine ,主要用于简单直接的string返回;
* AngularEngine 继承自 ViewEngine ,主要用于angularJS框架的前端代码,固定的全局js实例名称为cubeAngularJSObject;
* RaintplEngine 继承自 ViewEngine ,主要使用了Raintpl template ,感谢其提供了如此高效简单的php templete engine,帮助请参见./raintpl/example/page.html