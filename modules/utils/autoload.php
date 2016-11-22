<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/11/17
 * Time: 下午2:17
 */

namespace utils;

/**
 * Class Utils
 * @package utils
 */
class Utils
{
    /**
     * url is https protocol or not.
     *
     * @param $url
     * @return string
     */
    public static function isHTTPS($url)
    {
        return strstr('https://', pathinfo($url)['dirname']);
    }

    /**
     * check the php version.
     *
     * @param $version the lowest php version
     * @return bool
     */
    public static function is_legal_php_version($version = '5.6.0')
    {
        $kernel = explode('.', $version);
        $env = explode('.', explode('-', PHP_VERSION)[0]);

        if (intval($env[0]) < intval($kernel[0])) {
            return false;
        } elseif (intval($env[0]) > intval($kernel[0])) {
            return true;
        }
        if (intval($env[1]) < intval($kernel[1])) {
            return false;
        } elseif (intval($env[1]) > intval($kernel[1])) {
            return true;
        }
        if (intval($env[2]) < intval($kernel[2])) {
            return false;
        }
        return true;
    }

    /**
     * check the needed extensions.
     *
     * @param $extensions
     * @return bool
     */
    public static function is_miss_ext($extensions = null)
    {
        if (empty($extensions)) {
            return false;
        }
        if(!is_array($extensions)){
            $extensions = [$extensions];
        }
        $env_extensions = get_loaded_extensions();
        foreach ($env_extensions as $key => $ext) {
            $env_extensions[$key] = strtolower($ext);
        }
        foreach ($extensions as $ext) {
            if (!in_array($ext, $env_extensions)) {
                return $ext;
            }
        }
        return false;
    }
}


/**
 * Class BaseDynamic.
 * @package cube\core
 */
class DynamicClass
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