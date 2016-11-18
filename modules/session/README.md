# Session MiddleWare.
### LocalSession(PHP-SELF-SESSION,$_SESSION)
* Class inheritance relation: DynamicClass->Session
* session_name(cookie key) ,please set the package.json-core-session_name
* session_timeout(cookie time) , please set the packagejson-core-session_timeout
```javascript
//Dependency Library
require __DIR__.'/../utils/Utils.php';

$router = Application::router();
$router->on(session\Session());
$router->on('/session',function($req,$res,$next){
    $res->send($req->session->username);
    $req->session->username = time();
});

```
### RemoteSession(RedisSession)
* Class inheritance relation: DynamicClass->RedisSession
* session_name(cookie key) ,please set the package.json-core-session_name
* session_timeout(cookie time) , please set the package.json-core-session_timeout
```javascript
//Dependency Library
requi
$router = Application::router();
$router->on(session\RedisSession([
    'host'=>'localhost',
    'port'=>'4396',
    'password'=>'',
    'db'=>1
]));
$router->on('/session',function($req,$res,$next){
    $res->send($req->session->username);
    $req->session->username = time();
});

```
* get the current session_id
```javascript
$req->session->getID();
```
* set session value
```javascript
$req->session->username = 'hello';
```
* get the session value
```javascript
echo $req->session->username;
```
* delete session.
```javascript
$req->session->delete('key');
```
* clear all session.
```javascript
$req->session->clear();
```
* if you want to change the session_name和session_timeout, please change the core options['session_name','session_timeout'] in the package.json config file.