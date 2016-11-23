<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/11/9
 */
use cube\App;
use cookie\Cookie;
use session\Session;
use body\Body;

$app = App::app();

//cookie parser middleWare.
$app->on(Cookie::create());
//session parser middleWare.
$app->on(Session::create());
//body parser middleWare.
$app->on(Body::create());

//add common middleWare.
$app->on(function ($req, $res, $next) {
    $next();
});

//add router path.
$app->on('user/', 'router/user.php');
$app->on('/upload', 'router/upload.php');

//add router middleWare.
$app->on('/', function ($req, $res, $next) {
    $res->render(new \engine\EchoEngine(), 'index');
});