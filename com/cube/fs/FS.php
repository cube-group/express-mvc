<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/8/26
 * Time: 下午10:53
 */

namespace com\cube\fs;

use com\cube\core\Application;

/**
 * Class FS.
 * FileSystem.
 * @package com\cube\fs
 */
final class FS
{
    private function __construct()
    {
    }

    /**
     * move the file or dir.
     * @param $source
     * @param $des
     * @return bool
     */
    public static function move($source, $des)
    {
        if (!is_file($source)) {
            return false;
        }
        try {
            @rename($source, $des);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * copy the file.
     *
     * @param $source
     * @param $des
     * @return bool
     */
    public static function copy($source, $des)
    {
        if (!is_file($source)) {
            return false;
        }
        try {
            @copy($source, $des);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * delete the file.
     *
     * @param $source
     * @return bool
     */
    public static function remove($source)
    {
        if (!is_file($source)) {
            return false;
        }
        try {
            @unlink($source);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * create the file.
     *
     * @param $source
     * @param $data
     */
    public static function create($source, $data)
    {
        if (!is_writable($source)) {
            return false;
        }
        try {
            $file = fopen($source, 'w');
            fwrite($file, $data);
            fclose($file);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * append the content to the file.
     *
     * @param $source
     * @param $data
     */
    public static function append($source, $data)
    {
        if (!is_file($source) || !is_writable($source)) {
            return false;
        }
        try {
            $file = fopen($source, 'a');
            fwrite($file, $data);
            fclose($file);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * read the content from the fle.
     *
     * @param $source
     * @param $length content length
     */
    public static function read($source, $length = 0)
    {
        if (!is_file($source) || !is_readable($source)) {
            return '';
        }
        try {
            if ($length <= 0 || $length > filesize($source)) {
                return file_get_contents($source);
            }

            $file = fopen($source, 'r');
            $content = fread($file, $length);
            fclose($file);
            return $content;
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * get the content from the normal php input.
     *
     * @return string
     */
    public static function input()
    {
        return file_get_contents("php://input");
    }

    /**
     * put the format php input stream into the temporary file.
     *
     * array(
     *      array('tmp'=>'file name','path'=>'file path name')
     * );
     * @param null $content file content
     * @param string $key file name
     * @return array|null
     * @throws \Exception
     */
    public static function saveInputAsFile($content, $key = '', $options = null)
    {
        if (empty($content)) {
            return null;
        }
        $tmp_file_name = md5(time() + rand(0, 100000)) . (empty($key) ? '' : '_' . $key);
        $tmp_file_path = Application::$upload_dir . $tmp_file_name;
        file_put_contents($tmp_file_path, $content);
        return array(array('name' => $tmp_file_name, 'path' => $tmp_file_path));
    }

    /**
     * put the format upload files into the temporary files.
     * array(
     *      array('tmp'=>'file name','path'=>'file path name'),
     *      array('tmp'=>'file name','path'=>'file path name'),
     *      array('tmp'=>'file name','path'=>'file path name')
     * );
     * @param $options config
     * @return array|null
     */
    public static function saveUploadAsFile($options = null)
    {
        if (count($_FILES) > 0) {
            $files = array();
            foreach ($_FILES as $file) {
                if (count($file['name']) == 1) {
                    /**
                     * support different file name.
                     * <input type='file' name='file1'/>
                     * <input type='file' name='file2'/>
                     * array(2) { ["files1"]=> array(5) { ["name"]=> string(12) "IMG_4042.PNG" ["type"]=> string(9) "image/png" ["tmp_name"]=> string(39) "/usr/local/nginx/html/fs/temp/phphC7PiD" ["error"]=> int(0) ["size"]=> int(390315) } ["files2"]=> array(5) { ["name"]=> string(12) "IMG_4043.PNG" ["type"]=> string(9) "image/png" ["tmp_name"]=> string(39) "/usr/local/nginx/html/fs/temp/php1DMbhl" ["error"]=> int(0) ["size"]=> int(487587) } }
                     */
                    array_push($files, $file);
                } else {
                    /**
                     * support the same file name.
                     * <input type='file' name='files[]'/>
                     * array(1) { ["files"]=> array(5) { ["name"]=> array(2) { [0]=> string(12) "IMG_4042.PNG" [1]=> string(12) "IMG_4043.PNG" } ["type"]=> array(2) { [0]=> string(9) "image/png" [1]=> string(9) "image/png" } ["tmp_name"]=> array(2) { [0]=> string(39) "/usr/local/nginx/html/fs/temp/phpA4eEvt" [1]=> string(39) "/usr/local/nginx/html/fs/temp/phpZ2Ri0w" } ["error"]=> array(2) { [0]=> int(0) [1]=> int(0) } ["size"]=> array(2) { [0]=> int(390315) [1]=> int(487587) } } }
                     */
                    foreach ($file['name'] as $key1 => $name) {
                        $files[$key1] = array('name' => $name);
                    }
                    $params = ['type', 'tmp_name', 'error', 'size'];
                    foreach ($params as $param) {
                        foreach ($file[$param] as $key2 => $value) {
                            $files[$key2][$param] = $value;
                        }
                    }
                }
            }
            return self::save_upload($files);
        }
        return null;
    }

    private static function save_upload($files)
    {
        $stack = array();

        foreach ($files as $file) {
            if ($file['error'] > 0) {
                array_push($stack, array('name' => $file['name'], 'path' => ''));
                continue;
            }
            $tmp_file_name = md5(time() + rand(0, 100000)) . '_' . $file['name'];
            $tmp_file_path = Application::$upload_dir . $tmp_file_name;
            if (move_uploaded_file($file['tmp_name'], $tmp_file_path)) {
                array_push($stack, array('name' => $tmp_file_name, 'path' => $tmp_file_path));
            }
        }

        return $stack;
    }
}