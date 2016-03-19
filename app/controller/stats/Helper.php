<?php
/**
 * Created by PhpStorm.
 * User: wangd
 * Date: 2016/3/19
 * Time: 10:15
 */

namespace controller\stats;

class Helper {
    function statsWeekOrMonth($i, $t, $target) {
        $pdo = new \db\Base();
        $tables = array(
            'order_item' => 'o',
            'prototype' => 'p',
        );

        $year = date('Y');

        if($t == 'shoe') $manufactory = 'p.manufactory!=\'小商品\'';
        else $manufactory = 'p.manufactory=\'小商品\'';

        if($target == 'week') {
            $statsFields = 'sum(o.price) as amount, count(*) as quantity, weekofyear(o.create_time) as i';
            $time = 'weekofyear(o.create_time)='.$i;
            $meta = array(
                'full' => 'Week',
                'short' =>  'W',
                'chinese' => '周'
            );
        } else {
            $statsFields = 'sum(o.price) as amount, count(*) as quantity, month(o.create_time) as i';
            $time = 'month(o.create_time)='.$i;
            $meta = array(
                'full' => 'Month',
                'short' =>  'M',
                'chinese' => '月'
            );
        }

        $stats = $pdo->_fetchArray(
            $tables,
            $statsFields,
            array(array('o.prototype_id=p.id and '.$manufactory.' and year(o.create_time)=?', $year)),
            array('group' => 'i', 'order' => 'i'),
            0, 0, 0
        );
        foreach($stats as $idx=>&$item) {
            if($idx == 0 || $stats[$idx-1]['amount'] == 0) {
                $item['amountRatio'] = 'undefined';
            } else {
                $item['amountRatio'] = sprintf('%.2f%%', ($item['amount'] - $stats[$idx-1]['amount'])/$stats[$idx-1]['amount']);
            }
            if($idx == 0 || $stats[$idx-1]['quantity'] == 0) {
                $item['quantityRatio'] = 'undefined';
            } else {
                $item['quantityRatio'] = sprintf('%.2f%%', ($item['quantity'] - $stats[$idx-1]['quantity'])/$stats[$idx-1]['quantity']);
            }
        }

        $channelStats = $pdo->_fetchArray(
            $tables,
            'o.channel, sum(o.price) as amount, count(*) as quantity',
            array(array('o.prototype_id=p.id and '.$manufactory.' and '.$time)),
            array('group' => 'o.channel', 'order' => 'quantity desc'),
            0, 0, 0
        );

        $sizeStats = $pdo->_fetchArray(
            $tables,
            'o.size, sum(o.price) as amount, count(*) as quantity',
            array(array('o.prototype_id=p.id and '.$manufactory.' and '.$time)),
            array('group' => 'o.size', 'order' => 'quantity desc'),
            0, 0, 0
        );

        $modelStats = $pdo->_fetchArray(
            $tables,
            'p.model, sum(o.price) as amount, count(*) as quantity',
            array(array('o.prototype_id=p.id and '.$manufactory.' and '.$time)),
            array('group' => 'p.model', 'order' => 'quantity desc'),
            0, 0, 0
        );

        global $smarty;
        $smarty->assign('title', '销售统计');
        $smarty->assign('i', $i);
        $smarty->assign('t', $t);
        $smarty->assign('meta', $meta);
        $smarty->assign('year', $year);
        $smarty->assign('stats', $stats);
        $smarty->assign('channelStats', $channelStats);
        $smarty->assign('sizeStats', $sizeStats);
        $smarty->assign('modelStats', $modelStats);
        $smarty->display('stats/week_month.tpl');
    }
}