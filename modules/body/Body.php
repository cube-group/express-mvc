<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/9/12
 * Time: 下午5:09
 */

namespace body;
use utils\DynamicClass;

/**
 * Class Body.
 * implement app->request->body.
 * mimeType: text/html、text/xml、application/octet-stream等
 * @package modules\body
 */
class Body
{
    /**
     * Create the middleWare Instance.
     * @return \Closure
     */
    public static function create()
    {
        return function ($req, $res, $next) {
            $req->query(new QueryInstance());
            $req->body(new BodyInstance());

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


/**
 * Class BodyInstance.
 * @package modules\body
 */
class BodyInstance extends DynamicClass
{
    public function __construct()
    {
        $this->body = $_POST;
    }

    public function __set($name, $value)
    {
        throw new \Exception('body not allowed to set!');
    }

    /**
     * the request method is post or not.
     * @return bool
     */
    public function post()
    {
        return count($_POST) > 0;
    }

    /**
     * get php://input content.
     * (not contains enctype="multipart/form-data").
     * @return string
     */
    public function content()
    {
        return file_get_contents("php://input");
    }

    /**
     * get the http upload file numbers.
     * @return int
     */
    public function fileNumber()
    {
        return count($_FILES);
    }
}