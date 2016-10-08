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
        echo json_encode(\utils\Tag::getTags(), JSON_UNESCAPED_UNICODE);
    }
} 
