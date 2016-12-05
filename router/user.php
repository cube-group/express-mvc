<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/11/15
 * Time: 上午10:54
 */

use cube\App;

$router = App::Router();

$router->on('/', function ($req, $res, $next) {
    //$req->route => /user/
    $next();
});

$router->on('/:id/:name/pwd', function ($req, $res, $next) {
    //$req->route => /user/:id/:name/:pwd
    $res->json([$req->params->id, $req->params->name]);
});

$router->on('/http', function ($req, $res, $next) {
    //$req->route => /user/http
    $res->send(\http\Http::get('https://github.com/cube-group'));
});

$router->on('/repeat/', 'router/upload.php');

$router->on('/db', function ($req, $res, $next) {
    \orm\DB::init([
        'host' => 'localhost',
        'port' => 3306,
        'db' => 'system',
        'username' => 'root',
        'password' => '',
        'prefix' => ''
    ]);
    $res->json(\orm\DB::model('user')->select());
});