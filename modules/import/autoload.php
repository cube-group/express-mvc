<?php
/**
 * Global load php method.
 * import will check the constant('BASE_DIR')
 *
 * @param $files file path string or array
 */
function import($files)
{
    $arr = null;
    if (empty($files)) {
        return;
    } elseif (is_array($files)) {
        $arr = $files;
    } else {
        $arr = [$files];
    }
    $base_dir = defined('BASE_DIR') ? constant('BASE_DIR') : '';
    foreach ($arr as $file) {
        $file = $base_dir . $file;
        if (!strstr($file, '.php')) {
            $file .= '.php';
        }
        if (!is_file($file)) {
            continue;
        }

        $key = 'import-require-once-' . $file;
        if (!isset($GLOBALS[$key])) {
            $GLOBALS[$key] = 1;
            require $file;
        }
    }
}