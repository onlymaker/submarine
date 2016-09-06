<?php
/**
 * Created by PhpStorm.
 * User: jibo
 * Date: 16-9-6
 * Time: 19:40
 */

namespace controller\stats;

class Tag extends Base {
    function get() {
        global $smarty;
        $smarty->assign("title", "Tag");
        $smarty->assign("description", "list");
        $smarty->display("stats/tag.tpl");
    }

    function post() {
        $content = explode("\r\n", substr(file_get_contents(ROOT . "/static/js/tag.js"), strlen("var tag = ")));
        array_shift($content);
        array_pop($content);
        $rows = array();
        foreach($content as $tag) {
            $rows[] = substr(str_replace("]", "", str_replace("[", "", trim($tag))), 0, -1);
        }
        echo json_encode($rows, JSON_UNESCAPED_UNICODE);
    }
} 
