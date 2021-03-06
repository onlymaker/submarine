<?php
/**
 * Created by PhpStorm.
 * User: wangd
 * Date: 2016/3/19
 * Time: 10:15
 */

namespace controller\stats;

use db\SqlMapper;
use utils\Tag;

class Helper
{
    function statsByTime($y, $i, $t, $target = 'week')
    {
        $pdo = new \db\Base();
        /*$tables = array(
            'order_item' => 'o',
            'prototype' => 'p',
        );

        if ($t == 'shoe') {
            $manufactory = 'p.manufactory NOT IN (\'拖鞋\',\'小商品\',\'服装\',\'包\')';
        } else {
            $manufactory = 'p.manufactory IN (\'小商品\',\'服装\',\'包\')';
        }*/

        $year = $y;

        if ($target == 'quarter') {
            list($prev) = $pdo->_fetchArray(
                ['order_item' => 'o'],
                'sum(price) as amount, count(*) as quantity',
                [['year(create_time)=? and quarter(create_time)=4', ($year - 1)]],
                null, 0, 0, 0
            );
            $statsFields = 'sum(price) as amount, count(*) as quantity, quarter(o.create_time) as i';
            $time = 'year(o.create_time)=' . $year . ' and quarter(o.create_time)=' . $i;
            if ($i == 1) {
                $prevTime = 'year(o.create_time)=' . ($year - 1) . ' and quarter(o.create_time)=4';
            } else {
                $prevTime = 'year(o.create_time)=' . $year . ' and quarter(o.create_time)=' . ($i - 1);
            }
            $meta = array(
                'full' => 'Quarter',
                'short' => 'Q'
            );
        } else if ($target == 'month') {
            list($prev) = $pdo->_fetchArray(
                ['order_item' => 'o'],
                'sum(price) as amount, count(*) as quantity',
                [['year(create_time)=? and month(create_time)=12', ($year - 1)]],
                null, 0, 0, 0
            );
            $statsFields = 'sum(price) as amount, count(*) as quantity, month(o.create_time) as i';
            $time = 'year(o.create_time)=' . $year . ' and month(o.create_time)=' . $i;
            if ($i == 1) {
                $prevTime = 'year(o.create_time)=' . ($year - 1) . ' and month(o.create_time)=12';
            } else {
                $prevTime = 'year(o.create_time)=' . $year . ' and month(o.create_time)=' . ($i - 1);
            }
            $meta = array(
                'full' => 'Month',
                'short' => 'M'
            );
        } else {
            list($prev) = $pdo->_fetchArray(
                ['order_item' => 'o'],
                'sum(price) as amount, count(*) as quantity',
                [['year(create_time)=? and weekofyear(create_time)=?', ($year - 1), date('W', strtotime(($year - 1) . '-12-31'))]],
                null, 0, 0, 0
            );
            $statsFields = 'sum(price) as amount, count(*) as quantity, weekofyear(o.create_time) as i';
            $time = 'year(o.create_time)=' . $year . ' and weekofyear(o.create_time)=' . $i;
            if ($i == 1) {
                $prevTime = 'year(o.create_time)=' . ($year - 1) . ' and weekofyear(o.create_time)=' . date('W', strtotime(($year - 1) . '-12-31'));
            } else {
                $prevTime = 'year(o.create_time)=' . $year . ' and weekofyear(o.create_time)=' . ($i - 1);
            }
            $meta = array_merge(
                array(
                    'full' => 'Week',
                    'short' => 'W'
                ),
                $this->getWeekDate($year, $i)
            );
        }

        $stats = $pdo->_fetchArray(
            ['order_item' => 'o'],
            $statsFields,
            [['year(create_time)=?', $year]],
            array('group' => 'i', 'order' => 'i'),
            0, 0, 0
        );
        foreach ($stats as $idx => &$item) {
            if ($idx == 0) {
                if ($prev['amount'] == 0) $item['amountRatio'] = 'undefined';
                else $item['amountRatio'] = sprintf('%.2f%%', ($item['amount'] - $prev['amount']) / $prev['amount'] * 100);
                if ($prev['quantity'] == 0) $item['quantityRatio'] = 'undefined';
                else $item['quantityRatio'] = sprintf('%.2f%%', ($item['quantity'] - $prev['quantity']) / $prev['quantity'] * 100);
            } else {
                if ($stats[$idx - 1]['amount'] == 0) $item['amountRatio'] = 'undefined';
                else $item['amountRatio'] = sprintf('%.2f%%', ($item['amount'] - $stats[$idx - 1]['amount']) / $stats[$idx - 1]['amount'] * 100);
                if ($stats[$idx - 1]['quantity'] == 0) $item['quantityRatio'] = 'undefined';
                else $item['quantityRatio'] = sprintf('%.2f%%', ($item['quantity'] - $stats[$idx - 1]['quantity']) / $stats[$idx - 1]['quantity'] * 100);
            }
            if ($target != 'quarter' && $target != 'month') {
                $item = array_merge(
                    $item,
                    $this->getWeekDate($year, $item['i'])
                );
            }
        }

        $channelStats = $pdo->_fetchArray(
            ['order_item' => 'o'],
            'channel, sum(price) as amount, count(*) as quantity',
            [[$time]],
            array('group' => 'channel', 'order' => 'quantity desc'),
            0, 0, 0
        );
        $prevChannelStats = $pdo->_fetchArray(
            ['order_item' => 'o'],
            'channel, sum(price) as amount, count(*) as quantity',
            [[$prevTime]],
            array('group' => 'channel', 'order' => 'quantity desc'),
            0, 0, 0
        );
        foreach ($channelStats as &$item) {
            $item['quantityRatio'] = '';
            $item['amountRatio'] = '';
            foreach ($prevChannelStats as $prevItem) {
                if ($item['channel'] == $prevItem['channel']) {
                    if ($prevItem['quantity'] != 0) $item['quantityRatio'] = sprintf('%.2f%%', ($item['quantity'] - $prevItem['quantity']) / $prevItem['quantity'] * 100);
                    if ($prevItem['amount'] != 0) $item['amountRatio'] = sprintf('%.2f%%', ($item['amount'] - $prevItem['amount']) / $prevItem['amount'] * 100);
                    break 1;
                }
            }
        }

        $sizeStats = $pdo->_fetchArray(
            ['order_item' => 'o'],
            'size, sum(price) as amount, count(*) as quantity',
            [[$time]],
            array('group' => 'size', 'order' => 'quantity desc'),
            0, 0, 0
        );
        $prevSizeStats = $pdo->_fetchArray(
            ['order_item' => 'o'],
            'size, sum(price) as amount, count(*) as quantity',
            [[$prevTime]],
            array('group' => 'size', 'order' => 'quantity desc'),
            0, 0, 0
        );
        foreach ($sizeStats as &$item) {
            $item['quantityRatio'] = '';
            $item['amountRatio'] = '';
            foreach ($prevSizeStats as $prevItem) {
                if ($item['size'] == $prevItem['size']) {
                    if ($prevItem['quantity'] != 0) $item['quantityRatio'] = sprintf('%.2f%%', ($item['quantity'] - $prevItem['quantity']) / $prevItem['quantity'] * 100);
                    if ($prevItem['amount'] != 0) $item['amountRatio'] = sprintf('%.2f%%', ($item['amount'] - $prevItem['amount']) / $prevItem['amount'] * 100);
                    break 1;
                }
            }
        }

        $modelStats = $pdo->_fetchArray(
            ['order_item' => 'o'],
            'sku as model, sum(price) as amount, count(*) as quantity',
            [[$time]],
            array('group' => 'sku', 'order' => 'quantity desc'),
            0, 0, 0
        );
        $prevModelStats = $pdo->_fetchArray(
            ['order_item' => 'o'],
            'sku as model, sum(price) as amount, count(*) as quantity',
            [[$prevTime]],
            array('group' => 'sku', 'order' => 'quantity desc'),
            0, 0, 0
        );
        foreach ($modelStats as &$item) {
            $item['quantityRatio'] = '';
            $item['amountRatio'] = '';
            foreach ($prevModelStats as $prevItem) {
                if ($item['model'] == $prevItem['model']) {
                    if ($prevItem['quantity'] != 0) $item['quantityRatio'] = sprintf('%.2f%%', ($item['quantity'] - $prevItem['quantity']) / $prevItem['quantity'] * 100);
                    if ($prevItem['amount'] != 0) $item['amountRatio'] = sprintf('%.2f%%', ($item['amount'] - $prevItem['amount']) / $prevItem['amount'] * 100);
                    break 1;
                }
            }
        }

        $tags = Tag::getTags();
        $tagStats = array();
        $prevTagStats = array();
        foreach ($tags as $tag) {
            $db = SqlMapper::getDbEngine();
            $sql = <<<SQL
SELECT count(*) as quantity, sum(o.price) as sales
FROM order_item o, prototype p
WHERE {$time} AND o.prototype_id=p.id and p.tag='$tag'
SQL;
            trace("tag stats: " . $sql);
            list($result) = $db->exec($sql);
            $tagStats[] = array(
                "tag" => $tag,
                "quantity" => $result["quantity"],
                "amount" => $result["price"]
            );
            $sql = <<<SQL
SELECT count(*) as quantity, sum(o.price) as sales
FROM order_item o, prototype p
WHERE {$prevTime} AND o.prototype_id=p.id and p.tag='$tag'
SQL;
            trace("prev tag stats: " . $sql);
            list($result) = $db->exec($sql);
            $prevTagStats[] = array(
                "tag" => $tag,
                "quantity" => $result["quantity"],
                "amount" => $result["price"]
            );
        }
        foreach ($tagStats as $index => &$item) {
            if ($prevTagStats[$index]['quantity'] != 0) {
                $item['quantityRatio'] = sprintf('%.2f%%', ($item['quantity'] - $prevTagStats[$index]['quantity']) / $prevTagStats[$index]['quantity'] * 100);
            } else {
                $item['quantityRatio'] = '';
            }
            if ($prevTagStats[$index]['amount'] != 0) {
                $item['amountRatio'] = sprintf('%.2f%%', ($item['amount'] - $prevTagStats[$index]['amount']) / $prevTagStats[$index]['amount'] * 100);
            } else {
                $item['amountRatio'] = '';
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
        $smarty->assign('tagStats', $tagStats);
        $smarty->display('stats/stats.tpl');
    }

    function getWeekDate($year, $weekNumber)
    {
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