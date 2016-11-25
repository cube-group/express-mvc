<?php

namespace http;

use utils\Utils;

if ($ext = Utils::is_miss_ext('curl')) {
    throw new \Exception('Ext ' . $ext . ' is not exist!');
}

/**
 * Class Http.
 * \http\Http
 * Copyright(c) 2016 Linyang.
 * MIT Licensed
 *
 * @package http
 */
final class Http
{
    private function __construct()
    {
    }

    /**
     * http/https get request.
     * @param $url
     * @param timeout
     * @param $CA
     * @return mixed
     */
    public static function get($url, $timeout = 15, $CA = '')
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);

        if (Utils::isHTTPS($url)) {
            if ($CA) {
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
                curl_setopt($curl, CURLOPT_CAINFO, $CA); //CA root file
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
            } else {
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1);
            }
        }
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    /**
     * http/https post request.
     * @param $url
     * @param $data
     * @param timeout
     * @param $CA
     * @return mixed
     */
    public static function post($url, $data, $timeout = 15, $CA = '')
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);

        if (Utils::isHTTPS($url)) {
            if ($CA) {
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
                curl_setopt($curl, CURLOPT_CAINFO, $CA); //CA root file
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
            } else {
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1);
            }
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    /**
     * post request by the special headers.
     * @param $url string
     * @param $data object
     * @param null $headers array
     * @param $timeout integer
     * @return string
     */
    public static function postData($url, $data, $header, $timeout = 15)
    {
        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => $header,
                'content' => $data,
                'timeout' => $timeout
            ]
        ];

        $context = stream_context_create($opts);
        $result = file_get_contents($url, false, $context);
        return $result;
    }
}

?>