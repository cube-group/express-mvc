<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/10/10
 * Time: 上午10:01
 */

namespace modules\image;

//扩展检测.
if (SystemUtil::check_unknown_extension('mongo')) {
    throw new CubeException('Mongo Ext Error.', CubeException::$EXT_ERROR);
}

/**
 * Class Image.
 * 图像处理类.
 * @package modules\image
 */
final class Image
{
    /**
     * 建议使用GD EXT 的扩展函数来做
     */
}