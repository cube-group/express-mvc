<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/8/30
 * Time: 下午2:41
 */

namespace cube\view;

use cube\core\Config;
use cube\fs\FS;

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
        return Config::get('BASE_DIR') . Config::get('dir', 'view') . '/' . $name . ".html";
    }
}