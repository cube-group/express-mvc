<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/9/27
 * Time: 下午10:27
 */

namespace modules\engine;

use cube\view\ViewEngine;
use cube\core\Config;
use modules\engine\raintpl\RainTPL;

/**
 * Class RaintplEngine.
 * 感谢Rain框架中的raintpl模板引擎提供支持.
 * @package modules\engine
 */
class RaintplEngine extends ViewEngine
{
    /**
     * RaintplEngine constructor.
     */
    public function __construct()
    {
        parent::__construct();

        Config::load('modules/engine/raintpl/rain.tpl.class.php');

        RainTPL::configure('base_url', null);
        RainTPL::configure("root_dir", Config::get('BASE_DIR'));
        RainTPL::configure("tpl_dir", Config::get('dir', 'view') . '/');
        RainTPL::configure("cache_dir", Config::get('dir', 'tmp') . '/');

    }

    public function render($name, $data = null)
    {
        $tpl = new RainTPL();

        if (!empty($data) && is_array($data)) {
            foreach ($data as $key => $value) {
                $tpl->assign($key, $value);
            }
        }

        echo $tpl->draw($name, $return_string = true);
    }
}