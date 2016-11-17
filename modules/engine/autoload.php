<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/8/30
 * Time: 下午2:41
 */

namespace engine;

use fs\FS;

require_once __DIR__.'/EchoEngine.php';
require_once __DIR__.'/AngularEngine.php';
require_once __DIR__.'/RaintplEngine.php';

/**
 * Class ViewEngine
 * @package com\cube\view
 *
 */
class ViewEngine
{
    public function __construct()
    {
    }

    /**
     * @param $name file name viewdir
     * @param $data array data
     */
    public function render($name, $data = null)
    {
        return FS::read($this->getViewPagePath($name));
    }

    /**
     * 获取view page 文件地址.
     * @param $name
     * @return string
     */
    final protected function getViewPagePath($name)
    {
        return constant('VIEW_DIR') . $name . ".html";
    }
}