<?php

namespace controller;

class Base extends \Prefab {
    function get() {
        global $f3, $smarty;
        $smarty->assign('context', $f3->get('BASE'));
        $smarty->display('base.tpl');
    }
}
