<?php
/**
 * Created by PhpStorm.
 * User: jibo
 * Date: 16-3-18
 * Time: 13:40
 */

namespace controller\stats;

class Index extends Base {
    function get() {
        global $f3;
        header("location:{$f3->BASE}/stats/Week");
    }
} 
