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
        $i = isset($_GET['i']) ? $_GET['i'] : 1;
        $t = isset($_GET['t']) ? $_GET['t'] : 'shoe';
        $helper = new Helper();
        $helper->statsWeekOrMonth($i, $t, 'month');
    }
} 
