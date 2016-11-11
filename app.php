<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/11/9
 * Time: 下午3:05
 */
use \com\cube\core\Application;
use \com\cube\core\Request;
use \com\cube\core\Response;
use \com\cube\core\Model;
use \com\cube\middleware\Connect;


$router = Application::router();

$router->on('/redirect',function(Request $req, Response $res, Connect $connect) {
    $res->redirect('./www.php?router=http');
});

$router->on('/mysql', function (Request $req, Response $res, Connect $connect) {
    $result = Model::get('mysql', 'parameters');
    $res->json($result);
});

$router->on('/cookie', function (Request $req, Response $res, Connect $connect) {
    $req->cookie->test_key = time();
    $res->json($req->cookie->test_key);
});

$router->on('/http', function (Request $req, Response $res, Connect $connect) {
    $content = \com\cube\http\Http::get('http://www.google.com');
    $res->send($content);
    $connect->next();
});

$router->on('/http/send', function (Request $req, Response $res, Connect $connect) {
    $res->send('http let me catch');
});

$router->on('/location', function (Request $req, Response $res, Connect $connect) {
    $res->location('http://www.google.com');
});

$router->on('/session', function (Request $req, Response $res, Connect $connect) {
    $res->json($req->session->username);
    $req->session->username = time();
});

$router->on('/send/:p1/:p2', function (Request $req, Response $res, Connect $connect) {
    $res->json($req->params);
});

$router->on('/upload', function (Request $req, Response $res, Connect $connect) {
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

$router->on('/download', function (Request $req, Response $res, Connect $connect) {
    $res->download('./README.md');
});

$router->on('/viewengine', function (Request $req, Response $res, Connect $connect) {
    $res->render(new \modules\engine\RaintplEngine(), 'page', array('title' => 'cube'));
});

$router->on('/', function (Request $req, Response $res, Connect $connect) {

    $content = Model::get('db', 'getUserCount');
    $res->json($content);
});