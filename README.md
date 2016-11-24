# php-mvc-express(like node.js express framework)
* I recently insisted on the idea of the core to the node.js-express framework.
* Seven days from Nodejs-express seamless switching to php-mvc-express development! :)
* Author by linyang created on 2016-08.

### ./package.json - config file
*  dir - dir config
*  core - core config
*  modules - modules loaded

### where is your log file???
* log.log - info/error/exception log
* sql.log - orm log

### app init
```javascript

//include all cube libs.
require './modules/import/autoload.php';
require './cube/App.php';

//initialize the cube framework.
\cube\App::init([
    'base_dir' => __DIR__, //root dir
    'time_limit' => 0, //program exec time limit (s)
    'error_report' => 1, //display error or not
    'time_zone' => 'Asia/Shanghai' //time zone
]);
```

### middleWare append
* initial mode middleWare.
```javascript
$router = App::Router();
$router->on(function($req,$res,$next){
    $a = 'helloWorld!'; //your code.
    $next(); //next middleWare.
    $a = ''; //gc
});
```
* router mode middleWare fileName.
```javascript
$router = App::Router();
$router->on('/test','router/test.php');
```
* router mode middleWare.
```javascript
$router = App::Router();
$router->on('/index',function($req,$res,$next){
    $a = 'helloWorld!'; //your code.
    $next(); //next middleWare.
    $a = ''; //gc
});
```
### ./www.php (the facade file of the Application)
Once the route configuration is included in the project configuration file,
it will automatically give priority to the path analysis of the virtual router,
otherwise the network address is used directly to resolve the path.
* Virtual router pathinfo
```javascript
./www.php?router=http (Cube Framework Application will find the router config from the package.json)

//application filter the pathinfo.
$router= App::Router();
$router.on('/http',function($req,$res,$next){
    $res->send('get');
    $next();
}
```
* Standard router pathinfo, change the nginx.conf
* url will be '/www.php/user/login' or '/www.php/user/login/www.php'
```javascript
location / {
    if (!-e $request_filename) {
        rewrite  ^/(.*)$  /index.php/$1  last;
        break;
    }
}

location ~ \.php {
    fastcgi_pass 127.0.0.1:9000;
    fastcgi_index www.php;
    include fastcgi_params;
    set $real_script_name $fastcgi_script_name;
    if ($fastcgi_script_name ~ "^(.+?\.php)(/.+)$") {
        set $real_script_name $1;
        set $path_info $2;
    }
    fastcgi_param SCRIPT_FILENAME $document_root$real_script_name;
    fastcgi_param SCRIPT_NAME $real_script_name;
    fastcgi_param PATH_INFO $path_info;
}
```

### GLOBAL CONST VALUE
* check defined('VALUE'), and use by constant('VALUE')
```javascript
//get the absolute project dir.
$result = constant('BASE_DIR');
//result: /User/xx/github/php-mvc-express/

//get the application startTime(ms).
$result = constant('START_TIME');
//result: as microtime(true)

//get the application view template dir.
$result = constant('VIEW_DIR');
//result:  /User/xx/github/php-mvc-express/view/

//get the application tmp(or upload) dir.
$result = constant('TMP_DIR');
//result:  /User/xx/github/php-mvc-express/tmp/

//get the application log.log path.
$result = constant('LOG_PATH');
//result:  /User/xx/github/php-mvc-express/log/log.log

//get the application sql.log path.
$result = constant('LOG_SQL_PATH');
//result:  /User/xx/github/php-mvc-express/log/sql.log

//get the application package.json json object.
$result = constant('CONFIG');
//result: package.json as array
```

