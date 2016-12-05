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
use query\Query;

$app = App::app();

//cookie parser middleware.
$app->on(Cookie::create());
//cookie parser middleware.
$app->on(Query::create());
//session parser middleware.
$app->on(Session::create());
//body parser middleware.
$app->on(Body::create());

//add common middleware.
$app->on(function ($req, $res, $next) {
    $next();
});

//add virtual router.
$app->on('/user', 'router/user.php');
$app->on('/upload', 'router/upload.php');


//add router middleware.
$app->on('/redirect', function ($req, $res, $next) {
    $res->redirect('/upload/');
});


//add router middleware.
$app->on('/', function ($req, $res, $next) {
    $res->render('index');
});
