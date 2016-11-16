<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/11/9
 */
use cube\core\Application;
use cube\core\Response;
use cube\core\Request;

$router = Application::router();

$router->on(function (Request $req, Response $res, $next) {
    //...auth code
    if (true) {
        $next();
    }
});

$router->on('/user', 'router/user.php');
$router->on('/upload', 'router/upload.php');