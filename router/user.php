<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/11/15
 * Time: 上午10:54
 */

use cube\Application;

$router = Application::router();

$router->on('/', function ($req, $res, $next) {
    //$req->route => /user/
    $next();
});

$router->on('/:id/:name/pwd', function ($req, $res, $next) {
    //$req->route => /user/:id/:name/:pwd
    $res->json($req->params);
});

$router->on('/http', function ($req, $res, $next) {
    //$req->route => /user/http
    $res->send(\http\Http::get('https://github.com/cube-group'));
});