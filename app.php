<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/11/9
 */
use cube\App;
use cube\Request;
use cube\Response;
use cookie\Cookie;
use session\Session;
use body\Body;

$router = App::router();

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

$router->on('/', function ($req, $res, $next) {
    $res->render(new \engine\EchoEngine(), 'index');
});