<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/9/27
 * Time: 下午10:27
 */

namespace engine;

use engine\raintpl\RainTPL;

/**
 * Class RaintplEngine.
 * 感谢Rain框架中的raintpl模板引擎提供支持.
 * @package modules\engine
 */
class RaintplEngine extends ViewEngine
{
    public function __construct()
    {
        require __DIR__ . '/raintpl/rain.tpl.class.php';

        RainTPL::$path_replace = false;
        RainTPL::configure('base_url', null);
        RainTPL::configure("root_dir", constant('BASE_DIR'));
        RainTPL::configure("tpl_dir", $GLOBALS['CONFIG']['dir']['view'] . '/');
        RainTPL::configure("cache_dir", $GLOBALS['CONFIG']['dir']['tmp'] . '/');
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