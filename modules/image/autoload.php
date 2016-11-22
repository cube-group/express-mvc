<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/10/10
 * Time: 上午10:01
 */

namespace image;

use utils\Utils;

if ($ext = Utils::is_miss_ext('imagick')) {
    throw new \Exception('Ext ' . $ext . ' is not exist!');
}

/**
 * Class Image.
 * @package modules\image
 */
final class Image
{
    /**
     * PDF TO PIC
     * @param $source  pdf filename
     * @param $tmp_path export the file dir path
     * @param $extension pic extension name
     * @param $page page number
     * @return array
     */
    public static function pdf2pic($source, $tmp_path = '/tmp/', $extension = 'jpg', $page = -1)
    {
        $im = new \Imagick();
        $im->setCompressionQuality(100);
        if ($page == -1) {
            $im->readImage($source);
        } else {
            $im->readImage($source . "[" . $page . "]");
        }
        foreach ($im as $key => $var) {
            $var->setImageFormat($extension);
            $filename = $tmp_path . "/" . $key . '.png';
            if ($var->writeImage($filename) == true) {
                $return[] = $filename;
            }
        }
        return $return;
    }

}

?>