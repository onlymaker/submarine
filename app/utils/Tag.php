<?php
/**
 * Created by PhpStorm.
 * User: jibo
 * Date: 2016/9/7
 * Time: 21:21
 */

namespace utils;

class Tag {
    public static function getTags() {
        $content = explode("\r\n", substr(file_get_contents(ROOT . "/static/js/tag.js"), strlen("var tag = ")));
        array_shift($content);
        array_pop($content);
        $size = count($content);
        $rows = array();
        foreach($content as $i=>$tag) {
            if(($i + 1) < $size) {
                $rows[] = substr(str_replace("]", "", str_replace("[", "", trim($tag))), 0, -1);
            } else {
                $rows[] = str_replace("]", "", str_replace("[", "", trim($tag)));
            }
        }
        return $rows;
    }
}