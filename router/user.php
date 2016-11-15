<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/11/15
 * Time: 上午10:54
 */

$router = \cube\core\Application::router();

$router->on('/', function ($req, $res, $next) {
    $res->send('catch user');
    $next();
});

echo __FILE__.'<br>';