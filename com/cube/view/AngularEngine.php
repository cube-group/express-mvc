<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/9/27
 * Time: 下午10:20
 */

namespace com\cube\view;

use com\cube\view\view;

/**
 * Class AngualrEngine.
 * @package modules\engine
 */
class AngularEngine extends ViewEngine
{
    public function __construct()
    {
        parent::__construct();
    }

    public function render($name, $data = null)
    {
        //remove all comments.
        $htmlData = preg_replace('#<!--[^\!\[]*?(?<!\/\/)-->#', '', parent::render($name));

        if (empty($data)) {
            echo $htmlData;
        } else {
            $angularData = '<script>';
            $angularData .= 'var cubeAngularJSObject = ';
            $angularData .= json_encode($data);
            $angularData .= ';</script>';

            if (strstr($htmlData, '<head>')) {
                $arr = explode('<head>', $htmlData);
                echo $arr[0] . '<head>' . $angularData . $arr[1];
            } else if (strstr($htmlData, '<html>')) {
                $arr = explode('<html>', $htmlData);
                echo $arr[0] . '<html><head>' . $angularData . '</head>' . $arr[1];
            } else {
                echo $angularData . $htmlData;
            }
        }
    }
}