<?php
/**
 * Created by PhpStorm.
 * User: jibo
 * Date: 16-3-18
 * Time: 13:40
 */

namespace controller\stats;

class Detail extends Base {
    function get() {
        $y = isset($_GET['y']) ? (int)$_GET['y'] : date('Y');
        $i = isset($_GET['i']) ? (int)$_GET['i'] : 1;
        $d = isset($_GET['d']) ? $_GET['d'] : 'Week';
        $model = isset($_GET['model']) ? $_GET['model'] : 'undefined';

        $pdo = new \db\Base();
        $tables = array(
            'order_item' => 'o',
            'prototype' => 'p',
        );
        if($d == 'Quarter') {
            $time = 'year(o.create_time)='.$y.' and quarter(o.create_time)='.$i;
            $description = $y.'年'.$i.'季度';
        } else if($d == 'Month') {
            $time = 'year(o.create_time)='.$y.' and month(o.create_time)='.$i;
            $description = $y.'年'.$i.'月';
        } else {
            $time = 'year(o.create_time)='.$y.' and weekofyear(o.create_time)='.$i;
            $description = $y.'年'.$i.'周';
        }

        $channelStats = $pdo->_fetchArray(
            $tables,
            'o.channel, sum(o.price) as amount, count(*) as quantity',
            array(array('o.prototype_id=p.id and p.model=? and '.$time, $model)),
            array('group' => 'o.channel', 'order' => 'quantity desc'),
            0, 0, 0
        );

        $sizeStats = $pdo->_fetchArray(
            $tables,
            'o.size, sum(o.price) as amount, count(*) as quantity',
            array(array('o.prototype_id=p.id and p.model=? and '.$time, $model)),
            array('group' => 'o.size', 'order' => 'quantity desc'),
            0, 0, 0
        );

        global $smarty;
        $smarty->assign('title', $model.' - 统计');
        $smarty->assign('model', $model);
        $smarty->assign('description', $description);
        $smarty->assign('channelStats', $channelStats);
        $smarty->assign('sizeStats', $sizeStats);
        $smarty->display('stats/detail.tpl');
    }
} 
