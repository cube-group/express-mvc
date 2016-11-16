<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/11/16
 * Time: 下午6:57
 */

use cube\core\Application;
use cube\fs\FS;

$router = Application::router();

$router->on('/', function ($req, $res, $next) {
    if ($req->body->fileNumber() > 0) {
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