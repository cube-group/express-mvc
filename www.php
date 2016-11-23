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
require './cube/App.php';

//start the mode of multiple kernel.
\cube\App::multiple(false);

//initialize the cube framework.
\cube\App::init([
    'base_dir' => __DIR__,
    'time_limit' => 0,
    'error_report' => 1,
    'time_zone' => 'Asia/Shanghai'
]);

?>