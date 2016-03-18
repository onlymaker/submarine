<?php
/**
 * Created by PhpStorm.
 * User: jibo
 * Date: 16-3-18
 * Time: 13:40
 */

namespace controller\stats;

class Week extends Base {
    function get() {
        global $smarty;
        $w = isset($_GET['w']) ? $_GET['w'] : 1;
        $t = isset($_GET['t']) ? $_GET['t'] : 'shoe';
        if($t == 'shoe') $manufactory = 'p.manufactory!=\'小商品\'';
        else $manufactory = 'p.manufactory=\'小商品\'';
        $pdo = new \db\Base();
        $tables = array(
            'order_item' => 'o',
            'prototype' => 'p',
        );

        $year = date('Y');

        list($stats) = $pdo->_fetchArray(
            $tables,
            'sum(o.price) as amount, count(*) as quantity',
            array(array('o.prototype_id=p.id and '.$manufactory.' and weekofyear(o.create_time)=?', $w)),
            null, 0, 0, 0
        );

        $weekStats = $pdo->_fetchArray(
            $tables,
            'sum(o.price) as amount, count(*) as quantity, weekofyear(o.create_time) as week',
            array(array('o.prototype_id=p.id and '.$manufactory.' and year(o.create_time)=?', $year)),
            array('group' => 'week', 'order' => 'week'),
            0, 0, 0
        );
        foreach($weekStats as $idx=>&$item) {
            if($idx == 0 || $weekStats[$idx-1]['amount'] == 0) {
                $item['amountRatio'] = 'undefined';
            } else {
                $item['amountRatio'] = sprintf('%.2f%%', ($item['amount'] - $weekStats[$idx-1]['amount'])/$weekStats[$idx-1]['amount']);
            }
            if($idx == 0 || $weekStats[$idx-1]['quantity'] == 0) {
                $item['quantityRatio'] = 'undefined';
            } else {
                $item['quantityRatio'] = sprintf('%.2f%%', ($item['quantity'] - $weekStats[$idx-1]['quantity'])/$weekStats[$idx-1]['quantity']);
            }
        }

        $channelStats = $pdo->_fetchArray(
            $tables,
            'o.channel, sum(o.price) as amount, count(*) as quantity',
            array(array('o.prototype_id=p.id and '.$manufactory.' and weekofyear(o.create_time)=?', $w)),
            array('group' => 'o.channel', 'order' => 'quantity desc'),
            0, 0, 0
        );

        $sizeStats = $pdo->_fetchArray(
            $tables,
            'o.size, sum(o.price) as amount, count(*) as quantity',
            array(array('o.prototype_id=p.id and '.$manufactory.' and weekofyear(o.create_time)=?', $w)),
            array('group' => 'o.size', 'order' => 'quantity desc'),
            0, 0, 0
        );

        $modelStats = $pdo->_fetchArray(
            $tables,
            'p.model, sum(o.price) as amount, count(*) as quantity',
            array(array('o.prototype_id=p.id and '.$manufactory.' and weekofyear(o.create_time)=?', $w)),
            array('group' => 'p.model', 'order' => 'quantity desc'),
            0, 0, 0
        );

        $smarty->assign('title', '销售统计');
        $smarty->assign('w', $w);
        $smarty->assign('t', $t);
        $smarty->assign('year', $year);
        $smarty->assign('stats', $stats);
        $smarty->assign('weekStats', $weekStats);
        $smarty->assign('channelStats', $channelStats);
        $smarty->assign('sizeStats', $sizeStats);
        $smarty->assign('modelStats', $modelStats);
        $smarty->display('stats/week.tpl');
    }
} 
