<?php
/**
 * Created by PhpStorm.
 * User: wangd
 * Date: 2016/3/19
 * Time: 10:15
 */

namespace controller\stats;

class Helper {
    function statsWeekOrMonth($y, $i, $t, $target='week') {
        $pdo = new \db\Base();
        $tables = array(
            'order_item' => 'o',
            'prototype' => 'p',
        );

        if($t == 'shoe') $manufactory = 'p.manufactory!=\'小商品\'';
        else $manufactory = 'p.manufactory=\'小商品\'';

        $year = $y;

        if($target == 'quarter') {
            list($prev) = $pdo->_fetchArray(
                $tables,
                'sum(o.price) as amount, count(*) as quantity',
                array(array('o.prototype_id=p.id and '.$manufactory.' and year(o.create_time)=? and quarter(o.create_time)=4', date('Y', strtotime("$year -1 year")))),
                null, 0, 0, 0
            );
            $statsFields = 'sum(o.price) as amount, count(*) as quantity, quarter(o.create_time) as i';
            $time = 'year(o.create_time)='.$year.' and quarter(o.create_time)='.$i;
            if($i == 1) {
                $prevTime = 'year(o.create_time)='.($year-1).' and quarter(o.create_time)=4';
            }
            else {
                $prevTime = 'year(o.create_time)='.$year.' and quarter(o.create_time)='.($i-1);
            }
            $meta = array(
                'full' => 'Quarter',
                'short' =>  'Q'
            );
        } else if($target == 'month') {
            list($prev) = $pdo->_fetchArray(
                $tables,
                'sum(o.price) as amount, count(*) as quantity',
                array(array('o.prototype_id=p.id and '.$manufactory.' and year(o.create_time)=? and month(o.create_time)=12', date('Y', strtotime("$year -1 year")))),
                null, 0, 0, 0
            );
            $statsFields = 'sum(o.price) as amount, count(*) as quantity, month(o.create_time) as i';
            $time = 'year(o.create_time)='.$year.' and month(o.create_time)='.$i;
            if($i == 1) {
                $prevTime = 'year(o.create_time)='.($year-1).' and month(o.create_time)=12';
            }
            else {
                $prevTime = 'year(o.create_time)='.$year.' and month(o.create_time)='.($i-1);
            }
            $meta = array(
                'full' => 'Month',
                'short' =>  'M'
            );
        } else {
            list($prev) = $pdo->_fetchArray(
                $tables,
                'sum(o.price) as amount, count(*) as quantity',
                array(array('o.prototype_id=p.id and '.$manufactory.' and year(o.create_time)=? and weekofyear(o.create_time)=?', date('Y', strtotime("$year -1 year")), date('W', strtotime(date('Y-12-31', strtotime("$year -1 year")))))),
                null, 0, 0, 0
            );
            $statsFields = 'sum(o.price) as amount, count(*) as quantity, weekofyear(o.create_time) as i';
            $time = 'year(o.create_time)='.$year.' and weekofyear(o.create_time)='.$i;
            if($i == 1) {
                $prevTime = 'year(o.create_time)='.($year-1).' and weekofyear(o.create_time)='.date('W', strtotime(date('Y-12-31', strtotime("$year -1 year"))));
            }
            else {
                $prevTime = 'year(o.create_time)='.$year.' and weekofyear(o.create_time)='.($i-1);
            }
            $meta = array_merge(
                array(
                    'full' => 'Week',
                    'short' =>  'W'
                ),
                $this->getWeekDate($year, $i)
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
            if($idx == 0) {
                if($prev['amount'] == 0) $item['amountRatio'] = 'undefined';
                else $item['amountRatio'] = sprintf('%.2f%%', ($item['amount'] - $prev['amount']) / $prev['amount'] * 100);
                if($prev['quantity'] == 0) $item['quantityRatio'] = 'undefined';
                else $item['quantityRatio'] = sprintf('%.2f%%', ($item['quantity'] - $prev['quantity']) / $prev['quantity'] * 100);
            } else {
                if($stats[$idx-1]['amount'] == 0) $item['amountRatio'] = 'undefined';
                else $item['amountRatio'] = sprintf('%.2f%%', ($item['amount'] - $stats[$idx-1]['amount']) / $stats[$idx-1]['amount'] * 100);
                if($stats[$idx-1]['quantity'] == 0) $item['quantityRatio'] = 'undefined';
                else $item['quantityRatio'] = sprintf('%.2f%%', ($item['quantity'] - $stats[$idx-1]['quantity']) / $stats[$idx-1]['quantity'] * 100);
            }
            if($target != 'quarter' && $target != 'month') {
                $item = array_merge(
                    $item,
                    $this->getWeekDate($year, $item['i'])
                );
            }
        }

        $channelStats = $pdo->_fetchArray(
            $tables,
            'o.channel, sum(o.price) as amount, count(*) as quantity',
            array(array('o.prototype_id=p.id and '.$manufactory.' and '.$time)),
            array('group' => 'o.channel', 'order' => 'quantity desc'),
            0, 0, 0
        );
        $prevChannelStats = $pdo->_fetchArray(
            $tables,
            'o.channel, sum(o.price) as amount, count(*) as quantity',
            array(array('o.prototype_id=p.id and '.$manufactory.' and '.$prevTime)),
            array('group' => 'o.channel', 'order' => 'quantity desc'),
            0, 0, 0
        );
        foreach($channelStats as &$item) {
            $item['quantityRatio'] = '';
            $item['amountRatio'] = '';
            foreach($prevChannelStats as $prevItem) {
                if($item['channel'] == $prevItem['channel']) {
                    if($prevItem['quantity'] != 0) $item['quantityRatio'] = sprintf('%.2f%%', ($item['quantity'] - $prevItem['quantity']) / $prevItem['quantity'] * 100);
                    if($prevItem['amount'] != 0) $item['amountRatio'] = sprintf('%.2f%%', ($item['amount'] - $prevItem['amount']) / $prevItem['amount'] * 100);
                    break 1;
                }
            }
        }

        $sizeStats = $pdo->_fetchArray(
            $tables,
            'o.size, sum(o.price) as amount, count(*) as quantity',
            array(array('o.prototype_id=p.id and '.$manufactory.' and '.$time)),
            array('group' => 'o.size', 'order' => 'quantity desc'),
            0, 0, 0
        );
        $prevSizeStats = $pdo->_fetchArray(
            $tables,
            'o.size, sum(o.price) as amount, count(*) as quantity',
            array(array('o.prototype_id=p.id and '.$manufactory.' and '.$prevTime)),
            array('group' => 'o.size', 'order' => 'quantity desc'),
            0, 0, 0
        );
        foreach($sizeStats as &$item) {
            $item['quantityRatio'] = '';
            $item['amountRatio'] = '';
            foreach($prevSizeStats as $prevItem) {
                if($item['size'] == $prevItem['size']) {
                    if($prevItem['quantity'] != 0) $item['quantityRatio'] = sprintf('%.2f%%', ($item['quantity'] - $prevItem['quantity']) / $prevItem['quantity'] * 100);
                    if($prevItem['amount'] != 0) $item['amountRatio'] = sprintf('%.2f%%', ($item['amount'] - $prevItem['amount']) / $prevItem['amount'] * 100);
                    break 1;
                }
            }
        }

        $modelStats = $pdo->_fetchArray(
            $tables,
            'p.model, sum(o.price) as amount, count(*) as quantity',
            array(array('o.prototype_id=p.id and '.$manufactory.' and '.$time)),
            array('group' => 'p.model', 'order' => 'quantity desc'),
            0, 0, 0
        );
        $prevModelStats = $pdo->_fetchArray(
            $tables,
            'p.model, sum(o.price) as amount, count(*) as quantity',
            array(array('o.prototype_id=p.id and '.$manufactory.' and '.$prevTime)),
            array('group' => 'p.model', 'order' => 'quantity desc'),
            0, 0, 0
        );
        foreach($modelStats as &$item) {
            $item['quantityRatio'] = '';
            $item['amountRatio'] = '';
            foreach($prevModelStats as $prevItem) {
                if($item['model'] == $prevItem['model']) {
                    if($prevItem['quantity'] != 0) $item['quantityRatio'] = sprintf('%.2f%%', ($item['quantity'] - $prevItem['quantity']) / $prevItem['quantity'] * 100);
                    if($prevItem['amount'] != 0) $item['amountRatio'] = sprintf('%.2f%%', ($item['amount'] - $prevItem['amount']) / $prevItem['amount'] * 100);
                    break 1;
                }
            }
        }

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
        $smarty->display('stats/stats.tpl');
    }

    function getWeekDate($year, $weekNumber) {
        $firstDay = mktime(0, 0, 0, 1, 1, $year);
        $firstWeekDay = date('N', $firstDay);
        $firstWeekNumber = date('W', $firstDay);
        if ($firstWeekNumber == 1) {
            $day = (1 - ($firstWeekDay - 1)) + 7 * ($weekNumber - 1);
            $monday = date('Y-m-d', mktime(0, 0, 0, 1, $day, $year));
            $sunday = date('Y-m-d', mktime(0, 0, 0, 1, $day + 6, $year));
        } else {
            $day = (9 - $firstWeekDay) + 7 * ($weekNumber - 1);
            $monday = date('Y-m-d', mktime(0, 0, 0, 1, $day, $year));
            $sunday = date('Y-m-d', mktime(0, 0, 0, 1, $day + 6, $year));
        }
        return array(
            'monday' => $monday,
            'sunday' => $sunday);
    }
}