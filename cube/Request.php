<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/8/26
 * Time: 上午10:23
 */

namespace cube;

use log\Log;
use utils\DynamicClass;
use utils\Utils;

/**
 * Class Request.
 * Copyright(c) 2016 Linyang.
 * MIT Licensed
 * @package cube
 */
final class Request
{
    /**
     * user ip
     * @var string
     */
    public $ip = '';
    /**
     * request protocol
     * @var string
     */
    public $protocol = 'http';
    /**
     * request host
     * @var string
     */
    public $host = '';
    /**
     * standard uri.
     * @var string
     */
    public $uri = '';
    /**
     * http refer
     * @var string
     */
    public $refer = '';
    /**
     * router string
     * @var string
     */
    public $path = '';
    /**
     * original http/https url
     * @var string
     */
    public $baseUrl = '';
    /**
     * cookie instance
     * @var object
     */
    public $cookie;
    /**
     *  session instance
     * @var object
     */
    public $session;
    /**
     *  body instance
     * @var object
     */
    public $body;
    /**
     *  query instance
     * @var object
     */
    public $query;
    /**
     * /router/:id/:name,$params['id']
     * @var array
     */
    public $params;
    /**
     * current router filter string.
     * @var string
     */
    public $route;
    /**
     * it's true after exec App::redirect('').
     * @var bool
     */
    public $redirected = false;

    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->initCoreInfo();
        $this->initPathInfo();

        //request log.log.
        Log::log($this->baseUrl);
    }

    /**
     * init core info.
     *
     */
    private function initCoreInfo()
    {
        //common.
        $this->host = $_SERVER['HTTP_HOST'];
        $this->ip = $_SERVER['REMOTE_ADDR'];
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->referer = @$_SERVER['HTTP_REFERER'];
        $this->headers = $this->headers();
        $this->baseUrl = $this->protocol . '://' . $this->host . $this->uri;
    }

    /**
     * init path info.
     */
    private function initPathInfo()
    {
        $router = Config::get('core', 'router_querystring');
        $facade = Config::get('core', 'facade');
        $path = '';
        if (empty($router)) {
            $path = str_replace($facade, '', $_SERVER['PHP_SELF']);
        } else {
            if (isset($_GET[$router]) && !empty($_GET[$router])) {
                $path = substr($_GET[$router], 0, 1) == '/' ? $_GET[$router] : ('/' . $_GET[$router]);
                unset($_GET[$router]);
            }
        }

        $this->path = strtolower(Utils::pathFilter($path));
    }

    /**
     * post check.
     * @return boolean
     */
    public function post()
    {
        return count($_POST) > 0;
    }

    /**
     * get request headers
     * @return array
     */
    public function headers()
    {
        return array(
            "HTTP_CONTENT_LENGTH" => @$_SERVER["HTTP_CONTENT_LENGTH"],
            "HTTP_COOKIE" => @$_SERVER["HTTP_COOKIE"],
            "HTTP_ACCEPT_LANGUAGE" => @$_SERVER["HTTP_ACCEPT_LANGUAGE"],
            "HTTP_ACCEPT_ENCODING" => @$_SERVER["HTTP_ACCEPT_ENCODING"],
            "HTTP_USER_AGENT" => @$_SERVER["HTTP_USER_AGENT"],
            "HTTP_ACCEPT" => @$_SERVER["HTTP_ACCEPT"],
            "HTTP_CACHE_CONTROL" => @$_SERVER["HTTP_CACHE_CONTROL"],
            "HTTP_CONNECTION" => @$_SERVER["HTTP_CONNECTION"],
            "HTTP_HOST" => @$_SERVER["HTTP_HOST"],
        );
    }

    /**
     * set query instance.
     * @param $value
     */
    public function query($value)
    {
        $this->query = $value;
    }

    /**
     * set body instance.
     * @param $body
     */
    public function body($body)
    {
        $this->body = $body;
    }

    /**
     * set cookie instance.
     * @param $cookie
     */
    public function cookie($cookie)
    {
        $this->cookie = $cookie;
    }

    /**
     * set session instance.
     * @param $session
     */
    public function session($session)
    {
        $this->session = $session;
    }

    /**
     * set params instance.
     * @param $params
     */
    public function params($params)
    {
        $this->params = new DynamicClass($params);
    }
}