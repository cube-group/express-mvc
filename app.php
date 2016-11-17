<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/11/9
 */
use cube\core\Application;
use cube\core\Response;
use cube\core\Request;
use modules\session\Session;
use modules\cookie\Cookie;
use modules\body\Body;


$router = Application::router();

//cookie parser.
$router->on(Cookie::create());
//session parser.
$router->on(Session::create());
//body parser.
$router->on(Body::create());


$router->on(function (Request $req, Response $res, $next) {
    //...auth code
    if (true) {
        $next();
    }
});

$router->on('/user', 'router/user.php');
$router->on('/upload', 'router/upload.php');