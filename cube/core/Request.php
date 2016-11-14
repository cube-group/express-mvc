<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/8/26
 * Time: 上午10:23
 */

namespace cube\core;

use cube\log\Log;

/**
 * Class Request.
 * @package com\cube\core
 */
final class Request
{
    /**
     * @var string user ip
     */
    public $ip = '';
    /**
     * @var string request protocol
     */
    public $protocol = 'http';
    /**
     * @var string request host
     */
    public $host = '';
    public $uri = '';
    /**
     * @var string http refer
     */
    public $refer = '';
    /**
     * @var string router string
     */
    public $path = '';
    /**
     * @var string original http/https url
     */
    public $baseUrl = '';
    /**
     * @var array all request headers(only read)
     */
    public $headers;
    /**
     * @var array cookie instance
     */
    public $cookie;
    /**
     * @var array session instance
     */
    public $session;
    /**
     * @var array body instance
     */
    public $body;
    /**
     * @var array query instance
     */
    public $query;
    /**
     * /router/:id/:name,$params['id']
     * @var
     */
    public $params;
    /**
     * current router filter string.
     * @var
     */
    public $route;

    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->initCoreInfo();
        $this->initPathInfo();

        //request log.
        Log::log($this->baseUrl);
    }

    private function initCoreInfo()
    {
        //common.
        $this->host = $_SERVER['HTTP_HOST'];
        $this->ip = $_SERVER['REMOTE_ADDR'];
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->referer = @$_SERVER['HTTP_REFERER'];
        $this->headers = $this->headers();
        $this->baseUrl = $this->protocol . '://' . $this->host . $this->uri;

        //queryString.
        $this->query = $_GET;
    }

    private function initPathInfo()
    {
        $router = Config::get('core', 'router');
        $facade = Config::get('core', 'facade');
        $path = '';
        if (empty($router)) {
            $path = str_replace($facade, '', $_SERVER['PHP_SELF']);
        } else {
            if (isset($_GET[$router]) && !empty($_GET[$router])) {
                $path = substr($this->query[$router], 0, 1) == '/' ? $_GET[$router] : ('/' . $_GET[$router]);
                unset($this->query[$router]);
            }
        }

        if (empty($path)) {
            $path = '/';
        } else if ($path != '/' && substr($path, -1) == '/') {
            $path = substr($path, 0, -1);
        }
        //lower string.
        $this->path = strtolower($path);
    }

    /**
     * post check.
     * @return mixed
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
     * set body instance.
     * @param BaseDynamic $cookie
     * @return array
     */
    public function body($body)
    {
        $this->body = $body;
    }

    /**
     * set cookie instance.
     * @param ICookie $cookie
     * @return array
     */
    public function cookie($cookie)
    {
        $this->cookie = $cookie;
    }

    /**
     * set session instance.
     * @return array
     */
    public function session($session)
    {
        $this->session = $session;
    }

    /**
     * set params instance.
     * @param BaseDynamic $params
     */
    public function params($params)
    {
        $this->params = $params;
    }
}