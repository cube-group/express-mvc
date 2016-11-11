<?php
/**
 * Created by PhpStorm.
 * www is the facade of the project.
 * never never try to change the www.
 * User: linyang
 * Date: 16/8/26
 * Time: 上午10:53
 */

//include all cube libs.
require 'com/cube/core/Application.php';

use com\cube\core\Application;
use modules\session\Session;
use modules\cookie\Cookie;
use modules\body\Body;

//initialize the cube framework.
$app = Application::getInstance()->init(__DIR__);

$router = Application::router();
//setup common init middleWares.
$router->on([
    new Cookie(),
    new Body(),
    new Session(),
//    new \modules\session_redis\RedisSession([
//        'host' => '127.0.0.1',
//        'port' => 6379,
//        'db' => 0,
//        'password' => 'your db password'
//    ])
]);

//start the cube service.
$app->startup();

?>