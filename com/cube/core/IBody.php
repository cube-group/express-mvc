<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/11/8
 * Time: 下午4:54
 */

namespace com\cube\core;

/**
 * Interface IBody.
 * @package com\cube\core
 */
interface IBody
{
    /**
     * the request method is post or not.
     * @return boolean
     */
    public function post();

    /**
     * file_get_contents('php://input');
     * @return string
     */
    public function content();

    /**
     * get the number of file upload , such as <input type='file'/>
     * @return integer
     */
    public function files_num();
}