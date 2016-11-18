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
require './modules/import/autoload.php';
require './cube/Application.php';

use cube\Application;

//initialize the cube framework.
Application::init(__DIR__)->start();

?>