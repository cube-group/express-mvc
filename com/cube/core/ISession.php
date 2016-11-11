<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/9/19
 * Time: 下午6:54
 */

namespace com\cube\core;


/**
 * Interface ISession.
 * @package com\cube\core
 */
interface ISession
{
    /**
     * get session_name.
     * @return mixed
     */
    public function getName();

    /**
     * get session_id.
     * @return mixed
     */
    public function getID();

    /**
     * delete the session
     * @param $options
     * @return mixed
     */
    public function delete($options);

    /**
     * clear session.
     * @return mixed
     */
    public function clear();
}