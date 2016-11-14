<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/10/10
 * Time: 上午10:01
 */

namespace modules\image;

//extension check.
if (SystemUtil::check_unknown_extension('gd')) {
    throw new \Exception('GD Ext Error.');
}

/**
 * Class Image.
 * 图像处理类.
 * @package modules\image
 */
final class Image
{
    private function __construct()
    {
    }
}