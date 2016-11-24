<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/8/26
 * Time: 上午10:23
 */

namespace cube;

use engine\ViewEngine;
use fs\FS;
use log\Log;


/**
 * Class Response
 * @package com\cube\core
 */
final class Response
{
    public function __construct()
    {
        header('ServiceX:' . Config::get('name') . ' version:' . Config::get('version'));
    }

    /**
     * url location to the client.
     * @param $path
     * @return $this
     */
    public function location($path)
    {
        header('Location:' . $path);
        return $this;
    }

    /**
     * file download.
     * @param $path
     */
    public function download($path)
    {
        header("Content-type: application/force-download");
        header("Content-Disposition: attachment; filename=" . pathinfo($path)['basename']);
        echo FS::read($path);

        Log::log('Response download', $this->getTimer());
    }

    /**
     * send the simple string to the client.
     * @param $value
     */
    public function send($value)
    {
        echo $value;

        Log::log('Response send', $this->getTimer());
    }

    /**
     * send the json string to the client.
     * @param $value
     */
    public function json($value)
    {
        echo json_encode($value);

        Log::log('Response json', $this->getTimer());
    }

    /**
     * send the jsonp string to the client.
     * @param $value
     * @return void
     */
    public function jsonp($value)
    {
        $callback_str = Config::get('core', 'jsonp');
        if (empty($callback_str)) {
            return;
        }
        $callback_func = $_GET[$callback_str];
        if (empty($callback_func)) {
            return;
        }
        echo $callback_func . '(' . json_encode($value) . ')';

        Log::log('Response jsonp', $this->getTimer());
    }

    /**
     * send the content to the client by the viewEngine.
     * @param $viewEngine ViewEngine
     * @param viewName string
     * @param $value object
     */
    public function render($viewEngine, $viewName, $value = null)
    {
        $viewEngine->render($viewName, $value);

        Log::log('Response render ' . $viewName, $this->getTimer());
    }

    /**
     * redirect url.
     * @param $value string
     */
    public function redirect($value)
    {
        $this->statusCode(301)->location($value);

        Log::log('Response redirect ' . $value, $this->getTimer());
    }

    /**
     * set httpHeader status.
     * @param $code integer
     */
    public function statusCode($code)
    {
        $_status = array(
            // Informational 1xx
            100 => 'Continue',
            101 => 'Switching Protocols',
            // Success 2xx
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            // Redirection 3xx
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Moved Temporarily ',  // 1.1
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            // 306 is deprecated but reserved
            307 => 'Temporary Redirect',
            // Client Error 4xx
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            // Server Error 5xx
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            509 => 'Bandwidth Limit Exceeded'
        );
        if (array_key_exists($code, $_status)) {
            header('HTTP/1.1 ' . $code . ' ' . $_status[$code]);
        }

        return $this;
    }


    /**
     * Get the application run duration microtime.
     *
     * @return int
     */
    public function getTimer()
    {
        return intval((microtime(true) - constant('START_TIME')) * 1000);
    }
}