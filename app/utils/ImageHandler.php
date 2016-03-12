<?php

namespace utils;

class ImageHandler {
    /**
     * 对图片做 Resize 操作
     *
     * @param string $src 源图片目录绝对路径
     * @param string $name 源图片文件名
     * @param string $thumb 目标图片目录绝对路径
     * @param string $thumbName 目标图片文件名
     * @param int $width 宽度
     * @param int $height 高度
     */
    public static function resizeImage(
        $src,
        $name,
        $thumb,
        $thumbName,
        $width,
        $height
    ) {
        $convert = false;
        if (extension_loaded('imagick')) {
            try{
                $img = new \Imagick($src.$name);
                $img->stripimage(); //去除图片信息
                $img->setimagecompressionquality(95);
                $img->thumbnailimage($width, $height, true);
                $img->writeimage($thumb.$thumbName);
                $img->destroy();
                unset($img);
                $convert = true;
            } catch(\Exception $e) {
            }
        }
        if (!$convert) {
            // F3 框架的 Image 类限制只能操作 UI 路径中的文件，所以我们这里需要设置 UI 路径
            global $f3;
            $f3->set('UI', $src);
            $img = new \Image($name);
            $img->resize($width, $height, false);
            $img->dump('jpeg', $thumb.$thumbName);
            $img->__destruct();
            unset($img);
        }
    }
}
