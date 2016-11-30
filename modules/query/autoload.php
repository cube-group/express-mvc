<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/9/12
 * Time: 下午5:09
 */

namespace query;

use utils\DynamicClass;

/**
 * Class Query.
 * implement Request->query.
 * @package modules\body
 */
class Query
{
    /**
     * Create the middleWare Instance.
     * @return \Closure
     */
    public static function create()
    {
        return function ($req, $res, $next) {
            $req->query(new QueryInstance());

            $next();
        };
    }
}


/**
 * Class QueryInstance
 * @package modules\body
 */
class QueryInstance extends DynamicClass
{
    public function __construct()
    {
        $this->body = $_GET;
    }
}