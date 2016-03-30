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
        global $smarty;

        $y = isset($_GET['y']) ? (int)$_GET['y'] : date('Y');
        $i = isset($_GET['i']) ? (int)$_GET['i'] : 1;
        $d = isset($_GET['d']) ? $_GET['d'] : 'Week';
        $mode = 'undefined';
        if(isset($_GET['model'])) {
            $mode = 'model';
            $model = $_GET['model'];
        } else if(isset($_GET['size'])) {
            $mode = 'size';
            $size = $_GET['size'];
        } else if(isset($_GET['channel'])) {
            $mode = 'channel';
            $channel = $_GET['channel'];
        }

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

        switch ($mode) {
            case 'channel':
                $modelStats = $pdo->_fetchArray(
                    $tables,
                    'p.model, count(*) as quantity',
                    array(array('o.prototype_id=p.id and o.channel=? and '.$time, $channel)),
                    array('group' => 'p.model', 'order' => 'quantity desc'),
                    0, 0, 0
                );
                $channelStats = null;
                $sizeStats = $pdo->_fetchArray(
                    $tables,
                    'o.size, count(*) as quantity',
                    array(array('o.prototype_id=p.id and o.channel=? and '.$time, $channel)),
                    array('group' => 'o.size', 'order' => 'quantity desc'),
                    0, 0, 0
                );
                $smarty->assign('title', $channel);
                break;
            case 'size':
                $modelStats = $pdo->_fetchArray(
                    $tables,
                    'p.model, count(*) as quantity',
                    array(array('o.prototype_id=p.id and o.size=? and '.$time, $size)),
                    array('group' => 'p.model', 'order' => 'quantity desc'),
                    0, 0, 0
                );
                $channelStats = $pdo->_fetchArray(
                    $tables,
                    'o.channel, count(*) as quantity',
                    array(array('o.prototype_id=p.id and o.size=? and '.$time, $size)),
                    array('group' => 'o.channel', 'order' => 'quantity desc'),
                    0, 0, 0
                );;
                $sizeStats = null;
                $smarty->assign('title', $size);
                break;
            case 'model':
            default:
                $modelStats = null;
                $channelStats = $pdo->_fetchArray(
                    $tables,
                    'o.channel, count(*) as quantity',
                    array(array('o.prototype_id=p.id and p.model=? and '.$time, $model)),
                    array('group' => 'o.channel', 'order' => 'quantity desc'),
                    0, 0, 0
                );
                $sizeStats = $pdo->_fetchArray(
                    $tables,
                    'o.size, count(*) as quantity',
                    array(array('o.prototype_id=p.id and p.model=? and '.$time, $model)),
                    array('group' => 'o.size', 'order' => 'quantity desc'),
                    0, 0, 0
                );
                $smarty->assign('title', $model);
        }

        $smarty->assign('description', $description);
        $smarty->assign('mode', $mode);
        $smarty->assign('modelStats', $modelStats);
        $smarty->assign('channelStats', $channelStats);
        $smarty->assign('sizeStats', $sizeStats);
        $smarty->display('stats/detail.tpl');
    }
} 
