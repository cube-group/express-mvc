<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/9/29
 * Time: 上午10:49
 */

namespace com\cube\utils;


/**
 * Class DBUtil.
 * @package com\cube\utils
 */
class DBUtil
{
    const SQL_WHERE_STRING = array('and', 'or');

    /**
     * array('or'=>array('a=1','b=2','c<3'));
     * (a=1 or b=2 or c<3)
     * array('or'=>array('and'=>array('a=1','b=2'),array('c=3')));
     * (a=1 and b=2) or (c=3)
     * array('or'=>array('and'=>array('a=1','b=2'),array('c like %3')));
     * (a=1 and b=2) or (c like %3);
     * @param $query
     * @param string $type
     * @return string
     */
    public static function parseArrayToSQLSelect($query, $type = '')
    {
        if (empty($query)) {
            return '';
        } elseif (!is_array($query)) {
            return $query;
        } elseif (count($query) > 0) {
            $stack = array();
            foreach ($query as $key => $value) {
                if (in_array($key, self::SQL_WHERE_STRING)) {
                    array_push($stack, self::parseArrayToSQLSelect($value, $key));
                } else {
                    array_push($stack, $value);
                }
            }
            return '(' . join(' ' . (empty($type) ? '' : $type) . ' ', $stack) . ')';
        } else {
            return '';
        }
    }
}