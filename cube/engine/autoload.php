<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/8/30
 * Time: 下午2:41
 */

namespace cube\engine;

use \cube\fs\FS;

require __DIR__ . '/AngularEngine.php';
require __DIR__ . '/RaintplEngine.php';

/**
 * Class ViewEngine
 * @package cube\engine
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
        echo $this->getViewContent($name);
    }

    /**
     * 获取view page 文件地址.
     * @param $name
     * @return string
     */
    final protected function getViewContent($name)
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