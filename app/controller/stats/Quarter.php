<?php
/**
 * Created by PhpStorm.
 * User: jibo
 * Date: 16-3-18
 * Time: 13:40
 */

namespace controller\stats;

class Quarter extends Base {
    function get() {
        $y = isset($_GET['y']) ? (int)$_GET['y'] : date('Y');
        $i = isset($_GET['i']) ? (int)$_GET['i'] : 1;
        $t = isset($_GET['t']) ? $_GET['t'] : 'shoe';
        $helper = new Helper();
        $helper->statsByTime($y, $i, $t, 'quarter');
    }
} 
