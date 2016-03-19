<?php
/**
 * Created by PhpStorm.
 * User: jibo
 * Date: 16-3-18
 * Time: 13:40
 */

namespace controller\stats;

class Month extends Base {
    function get() {
        $y = isset($_GET['y']) ? (int)$_GET['y'] : date('Y');
        $i = isset($_GET['i']) ? (int)$_GET['i'] : 1;
        $t = isset($_GET['t']) ? (int)$_GET['t'] : 'shoe';
        $helper = new Helper();
        $helper->statsWeekOrMonth($y, $i, $t, 'month');
    }
} 
