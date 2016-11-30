<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/8/26
 * Time: 下午10:53
 */

namespace fs;

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
//        if (!is_writable($source)) {
//            return false;
//        }
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
//        if (!is_writable($source)) {
//            return false;
//        }
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
     * return [
     *      ['tmp'=>'file name','path'=>'file path name']
     * ];
     *
     * return [
     *      ['tmp'=>'file name','error'=>'size']
     * ];
     *
     * options [
     *      'size'=>102400,//size kb
     *      'type'=>['image/png','image/jpeg','image/jpg','image/gif','pdf','txt','html']
     * ]
     * @param $content content
     * @param $key fileName
     * @param $options
     * @return array|null
     * @throws \Exception
     */
    public static function saveInputAsFile($content, $key = '', $options = null)
    {
        if (empty($key)) {
            $key = md5(time() + rand(0, 10000));
        }
        if (empty($content)) {
            return [['name' => $key, 'error' => 'null']];
        }
        if ($options && $options['size'] && strlen($content) > $options['size']) {
            return [['name' => $key, 'error' => 'size']];
        }
        $key = time() . '-' . $key;
        $tmp_file = constant('TMP_DIR') . $key;
        if (file_put_contents($tmp_file, $content) > 0) {
            return [['name' => $key, 'path' => $tmp_file]];
        } else {
            return [['name' => $key, 'error' => 'write']];
        }
    }

    /**
     * put the format upload files into the temporary files.
     * return [
     *      array('tmp'=>'file name','path'=>'file path name'),
     *      array('tmp'=>'file name','path'=>'file path name'),
     *      array('tmp'=>'file name','path'=>'file path name')
     * ];
     *
     * options [
     *      'size'=>102400,//size kb
     *      'type'=>['image/png','image/jpeg','image/jpg','image/gif','pdf','txt','html']
     * ]
     *
     * @param $options
     * @return array|null
     */
    public static function saveUploadAsFile($options = null)
    {
        /**
         * once select - multiple upload.
         * <input name="files[]">
         * <input name="files[]">
         *
         * array (size=1)
         * 'files' =>
         * array (size=5)
         * 'name' =>
         * array (size=2)
         * 0 => string 'a-0.jpg' (length=7)
         * 1 => string 'cube-icon.png' (length=13)
         * 'type' =>
         * array (size=2)
         * 0 => string 'image/jpeg' (length=10)
         * 1 => string 'image/png' (length=9)
         * 'tmp_name' =>
         * array (size=2)
         * 0 => string '/tmp/phpE8JDrd' (length=14)
         * 1 => string '/tmp/phpRWD2w1' (length=14)
         * 'error' =>
         * array (size=2)
         * 0 => int 0
         * 1 => int 0
         * 'size' =>
         * array (size=2)
         * 0 => int 43596
         * 1 => int 4368
         */

        /**
         * once select - multiple upload.
         * <input name="files[]" multiple/>
         *
         * array (size=1)
         * 'files' =>
         * array (size=5)
         * 'name' =>
         * array (size=2)
         * 0 => string 'a-0.jpg' (length=7)
         * 1 => string 'cube-icon.png' (length=13)
         * 'type' =>
         * array (size=2)
         * 0 => string 'image/jpeg' (length=10)
         * 1 => string 'image/png' (length=9)
         * 'tmp_name' =>
         * array (size=2)
         * 0 => string '/tmp/phpE8JDrd' (length=14)
         * 1 => string '/tmp/phpRWD2w1' (length=14)
         * 'error' =>
         * array (size=2)
         * 0 => int 0
         * 1 => int 0
         * 'size' =>
         * array (size=2)
         * 0 => int 43596
         * 1 => int 4368
         */


        /**
         * multiple select - multiple upload.
         * <input name="file1">
         * <input name="file2">
         *
         * array (size=2)
         * 'file1' =>
         * array (size=5)
         * 'name' => string 'a-0.jpg' (length=7)
         * 'type' => string 'image/jpeg' (length=10)
         * 'tmp_name' => string '/tmp/phpmRuGBJ' (length=14)
         * 'error' => int 0
         * 'size' => int 43596
         * 'file2' =>
         * array (size=5)
         * 'name' => string 'cube-icon.png' (length=13)
         * 'type' => string 'image/png' (length=9)
         * 'tmp_name' => string '/tmp/phpuGDaDv' (length=14)
         * 'error' => int 0
         * 'size' => int 4368
         */

        if (count($_FILES) > 0) {
            $files = [];
            foreach ($_FILES as $file) {
                //multiple select.
                if (is_string($file['name'])) {
                    array_push($files, $file);
                } else {
                    //once select.
                    foreach ($file['name'] as $key1 => $name) {
                        $files[$key1] = ['name' => $name];
                    }
                    $params = ['type', 'tmp_name', 'error', 'size'];
                    foreach ($params as $param) {
                        foreach ($file[$param] as $key2 => $value) {
                            $files[$key2][$param] = $value;
                        }
                    }
                }
            }
            return self::save_upload($files, $options);
        }
        return null;
    }

    private static function save_upload($files, $options)
    {
        $stack = [];
        foreach ($files as $file) {
            if ($file['error']) {
                array_push($stack, ['name' => $file['name'], 'error' => $file['error']]);
                continue;
            }

            if ($options) {
                if ($options['size'] && $file['size'] > $options['size']) {
                    array_push($stack, ['name' => $file['name'], 'error' => 'size']);
                    continue;
                }
                if ($options['type'] && is_array($options['type']) && !in_array($file['type'], $options['type'])) {
                    array_push($stack, ['name' => $file['name'], 'error' => 'type']);
                    continue;
                }
            }

            $key = md5(time() + rand(0, 10000)) . '-' . $file['name'];
            $tmp_file = constant('TMP_DIR') . $key;
            if (move_uploaded_file($file['tmp_name'], $tmp_file)) {
                array_push($stack, ['name' => $key, 'path' => $tmp_file]);
            } else {
                array_push($stack, ['name' => $key, 'error' => 'write']);
            }
        }
        return $stack;
    }
}