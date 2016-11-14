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
    if(empty($req->query->username)){
        $res->send('auth error');
        return;
    }
    $next();
});


$router->on('/redirect', function (Request $req, Response $res, $next) {
    $res->redirect('./www.php?router=http');
});


$router->on('/mysql', function (Request $req, Response $res, $next) {
    $result = '';//to do your model/proxy data.
    $res->json($result);
});


$router->on('/cookie', function (Request $req, Response $res, $next) {
    $req->cookie->test_key = time();
    $res->json($req->cookie->test_key);
});


$router->on('/http', function (Request $req, Response $res, $next) {
    $content = \cube\http\Http::get('https://github.com/cube-group/');
    $res->send($content);
    $next();
});


$router->on('/http/send', function (Request $req, Response $res, $next) {
    $res->send('http let me catch');
});


$router->on('/location', function (Request $req, Response $res, $next) {
    $res->location('https://github.com/cube-group/');
});


$router->on('/session', function (Request $req, Response $res, $next) {
    $res->json($req->session->username);
    $req->session->username = time();
});


$router->on('/send/:p1/:p2', function (Request $req, Response $res, $next) {
    $res->json($req->params);
});


$router->on('/upload', function (Request $req, Response $res, $next) {
    if ($req->body->files_num() > 0) {
        $res->json(FS::saveUploadAsFile());
    } else {
        $content = $req->body->content();
        if (!empty($content)) {
            $res->json(FS::saveInputAsFile($content, $req->query->key));
        } else {
            $res->send('error');
        }
    }
});


$router->on('/download', function (Request $req, Response $res, $next) {
    $res->download('./README.md');
});


$router->on('/view', function (Request $req, Response $res, $next) {
    $res->render(new \modules\engine\RaintplEngine(), 'page', array('title' => 'cube'));
});


$router->on('/', function (Request $req, Response $res, $next) {
    $res->render(new \cube\view\EchoEngine(), 'index');
});