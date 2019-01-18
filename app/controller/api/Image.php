<?php

namespace controller\api;

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

class Image extends Base
{

    function beforeRoute()
    {
        header('Access-Control-Allow-Origin:*');
    }

    function post()
    {
        global $f3;

        $dir = $f3->get('IMAGE_DIR');
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $hashName = hash('md5', time());
        $fileName = $hashName . strrchr($_FILES['file']['name'], '.');
        file_put_contents($dir . $fileName, file_get_contents($_FILES['file']['tmp_name']));

        trace('Image upload:' . $dir . $fileName);

        $auth = new Auth($f3->get('QINIU_ACCESS_KEY'), $f3->get('QINIU_SECRET_KEY'));
        $bucket = $f3->get('QINIU_BUCKET');
        $token = $auth->uploadToken($bucket);
        $filePath = $dir . $fileName;
        $key = $fileName;
        $uploadMgr = new UploadManager();
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
        if ($err !== null) {
            ob_start();
            var_dump($err);
            trace(ob_get_clean());
        } else {
            unlink($dir . $fileName);
        }

        echo $fileName;
    }
}
