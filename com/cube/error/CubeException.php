<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/9/27
 * Time: 下午9:14
 */

namespace com\cube\error;

/**
 * Class CubeError.
 * @package com\cube\error
 */
class CubeException extends \Exception
{
    /**
     * version low.
     * @var int
     */
    public static $VERSION_ERROR = 10000;
    
    /**
     * lack of extension.
     * @var int
     */
    public static $EXT_ERROR = 10001;

    /**
     * unknow error.
     * @var int
     */
    public static $UNKNOW_ERROR = 10009;
}