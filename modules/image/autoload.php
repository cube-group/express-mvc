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
     * PPT TO PDF
     * you need setup the libreoffice and libreoffice-headless
     * yum install libreoffice libreoffice-headless
     * @param $source
     * @return bool|string
     */
    public static function ppt2pdf($source)
    {
        if (system('export DISPLAY=:0.0 && libreoffice --headless --invisible --convert-to pdf ' . $source) !== false) {
            $info = pathinfo($source);
            return $info['dirname'] . '/' . explode('.', $info['basename'])[0] . 'pdf';
        }
        return false;
    }

    /**
     * PDF TO PIC
     * @param $source  pdf filename
     * @param $path export the file dir path
     * @param $extension pic extension name
     * @param $page page number
     * @return array
     */
    public static function pdf2pic($source, $path, $extension = 'jpg', $page = -1)
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
            $filename = $path . "/" . $key . '.png';
            if ($var->writeImage($filename) == true) {
                $return[] = $filename;
            }
        }
        return $return;
    }

}

?>