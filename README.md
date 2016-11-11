# The framework named cube for the php.
* A simple and efficient development framework based on middleware, configuration and extension
* Author by linyang created on 2016-08.
* Version Beta 1.0.1

### ./com - framework dir(never change it)
* /com/cube - cube dir
* /com/cube/core - core classes
* /com/cube/db - orm
* /com/cube/error - Exception / Error
* /com/cube/fs - filesystem operate
* /com/cube/http - http/https request
* /com/cube/international - international
* /com/cube/log - log
* /com/cube/middleware - Connect MiddleWare & RouterMiddleWare
* /com/cube/utils - tools
* /com/cube/view - ViewEngine (EchoEngine & AngularEngine)


### ./package.json - config file
*  dir - dir config
*  framework - core config
*  engine - engine loaded
*  modules - modules loaded
*  model - proxy loaded(not instantiation)

### ./www.php (the facade file of the Application)
Once the route configuration is included in the project configuration file,
it will automatically give priority to the path analysis of the virtual router,
otherwise the network address is used directly to resolve the path.
* Virtual router pathinfo
```javascript
./www.php?router=http (Cube Framework Application will find the router config from the package.json)

//application filter the pathinfo.
$router= Application::Router();
$router.on('/http',function($req,$res,$connect){
    $res->send('get');
    $connect->next();
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

