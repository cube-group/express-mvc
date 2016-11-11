<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/9/12
 * Time: 下午1:53
 */

namespace com\cube\utils;

/**
 * Class ArrayUtil.
 * @package com\cube\utils
 */
final class ArrayUtil
{
    /**
     * remove the element by the key from the array.
     *
     * @param $data
     * @param $key
     * @return mixed
     */
    public static function removeByKey($data, $key)
    {
        if (!array_key_exists($key, $data)) {
            return $data;
        }
        $keys = array_keys($data);
        $index = array_search($key, $keys);
        if ($index !== false) {
            array_splice($data, $index, 1);
        }
        return $data;
    }

    /**
     * remove the element by the value from the array.
     * @param $data
     * @param $value
     */
    public static function removeByValue($data, $value)
    {
        if (array_search($value) == false) {
            return $data;
        }
        foreach ($data as $key => $item) {
            if ($item == $value) {
                unset($data[$key]);
            }
        }
        return $data;
    }
}