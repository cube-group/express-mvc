<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/11/15
 * Time: 上午10:32
 */

$router = \cube\core\Application::router();

$router->on('abc', function ($req, $res, $next) {
    $res->send($req->route);
});

echo __FILE__.'<br>';