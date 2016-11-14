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
require 'cube/core/Application.php';

use cube\core\Application;
use modules\session\Session;
use modules\cookie\Cookie;
use modules\body\Body;

//initialize the cube framework.
Application::init(__DIR__)->start(
    [
        Cookie::create(),
        Body::create(),
        Session::create()
    ]
);

?>