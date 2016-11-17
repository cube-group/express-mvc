# php-mvc-express(like node.js express framework)
* I recently insisted on the idea of the core to the node.js-express framework.
* Seven days from Nodejs-express seamless switching to php-mvc-express development! :)
* Author by linyang created on 2016-08.

### ./package.json - config file
*  dir - dir config
*  core - core config
*  modules - modules loaded

### middleWare append
* initial mode middleWare.
```javascript
$router = Application::router();
$router->on(function($req,$res,$next){
    $a = 'helloWorld!'; //your code.
    $next(); //next middleWare.
    $a = ''; //gc
});
```
* router mode middleWare fileName.
```javascript
$router = Application::router();
$router->on('/test','router/test.php');
```
* router mode middleWare.
```javascript
$router = Application::router();
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
$router= Application::Router();
$router.on('/http',function($req,$res,$next){
    $res->send('get');
    $next();
}
```
* Standard router pathinfo, change the nginx.conf
```javascript
location / {
    if (!-e $request_filename) {
        rewrite  ^/(.*)$  /index.php/$1  last;
        break;
    }
}

location ~ \.php {
    fastcgi_pass 127.0.0.1:9000;
    fastcgi_index index.php;
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

### fast and simple!
* You do not need to write code in the import file, simply by modifying the configuration file and the logic code to complete what you want!

