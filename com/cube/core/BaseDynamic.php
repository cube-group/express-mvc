<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/9/19
 * Time: 下午6:03
 */

namespace com\cube\core;

/**
 * Class BaseDynamic.
 * @package com\cube\core
 */
class BaseDynamic
{
    protected $body;

    public function __construct()
    {
        $this->body = array();
    }

    public function __get($name)
    {
        // TODO: Implement __get() method.
        return $this->body[$name];
    }

    public function __set($name, $value)
    {
        // TODO: Implement __set() method.
        $this->body[$name] = $value;
    }

    public function delete($name)
    {
        unset($this->body[$name]);
    }

    public function clear()
    {
        foreach ($this->body as $key => $value) {
            unset($this->body[$key]);
        }
    }
}