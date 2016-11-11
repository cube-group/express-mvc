<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/9/12
 * Time: 下午5:09
 */

namespace modules\body;

use com\cube\core\BaseDynamic;
use com\cube\core\IBody;
use com\cube\core\Response;
use com\cube\core\Request;
use com\cube\middleware\MiddleWare;

/**
 * Class Body.
 * implement app->request->body.
 * mimeType: text/html、text/xml、application/octet-stream等
 * @package modules\body
 */
class Body extends MiddleWare
{
    public function run(Request $req, Response $res)
    {
        $req->body(new BodyInstance());
    }
}


/**
 * Class BodyInstance.
 * @package modules\body
 */
class BodyInstance extends BaseDynamic implements IBody
{
    public function __construct()
    {
        $this->body = $_POST;
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
    public function files_num()
    {
        return count($_FILES);
    }
}