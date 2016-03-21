<?php
/**
 * Created by PhpStorm.
 * User: jibo
 * Date: 16-3-18
 * Time: 13:40
 */

namespace controller\profit;

class Month extends Base {
    function get() {
        $y = isset($_GET['y']) ? (int)$_GET['y'] : date('Y');
        $helper = new Helper();
        $helper->statsWeekOrMonth($y, 'month');
    }
} 
