<?php
/**
 * Created by PhpStorm.
 * User: jibo
 * Date: 16/3/12
 * Time: 10:32
 */

namespace rest;

use utils\ImageHandler;

class Image extends Base {

    function beforeRoute() {
        header('Access-Control-Allow-Origin:*');
    }

    function post() {
        global $f3;

        $dir = $f3->get('IMAGE_DIR');
        if(!is_dir($dir)) mkdir($dir, 0777, true);

        $hashName = hash('md5', time());
        $fileName = $hashName.strrchr($_FILES['file']['name'], '.');
        $thumbName = $hashName.'_thumb'.strrchr($_FILES['file']['name'], '.');
        file_put_contents($dir.$fileName, file_get_contents($_FILES['file']['tmp_name']));

        trace('Image upload:'.$dir.$fileName);

        ImageHandler::resizeImage($dir, $fileName, $dir, $thumbName, 300, 300);

        echo $thumbName;
    }
}
