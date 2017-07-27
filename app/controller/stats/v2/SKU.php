<?php

namespace controller\stats\v2;

use controller\stats\Base;
use DB\SQL\Mapper;
use db\SqlMapper;

class SKU extends Base
{
    function stats($f3)
    {
        global $smarty;
        $smarty->assign('title', 'SKU - stats');
        if ($f3->VERB == 'POST') {
            $params = [
                'model' => $_POST['sku'],
                'market' => $_POST['market'],
                'start' => $_POST['start-date'],
                'end' => $_POST['end-date'],
            ];
            $smarty->assign('data', $this->query($params));
            $smarty->display('stats/v2/sku_result.tpl');
        } else {
            $smarty->display('stats/v2/sku.tpl');
        }
    }

    function validate($f3)
    {
        $error = ['code' => 0];
        $db = SqlMapper::getDbEngine();
        $model = $_POST['sku'];
        $prototype = new Mapper($db, 'prototype');
        $prototype->load(['model = ?', $model]);
        if ($prototype->dry()) {
            $error['code'] = -1;
            $error['message'] = 'SKU NOT FOUND';
            echo json_encode(['error' => $error], JSON_UNESCAPED_UNICODE);
            return;
        }
        $start = strtotime($_POST['start']);
        $end = strtotime($_POST['end']);
        if ($end <= $start) {
            $error['code'] = -1;
            $error['message'] = '起始时间不能超过终止时间';
            echo json_encode(['error' => $error], JSON_UNESCAPED_UNICODE);
            return;
        }
        if (($end - $start) > (90 * 24 * 3600)) {
            $error['code'] = -1;
            $error['message'] = '起始时间与终止时间间隔大于90天';
            echo json_encode(['error' => $error], JSON_UNESCAPED_UNICODE);
            return;
        }
        echo json_encode(['error' => $error], JSON_UNESCAPED_UNICODE);
    }

    function query($params)
    {
        $model = $params['model'];
        $market = strtoupper($params['market']);
        $start = $params['start'];
        $end = $params['end'] . ' 23:59:59';

        $db = SqlMapper::getDbEngine();
        $prototype = new Mapper($db, 'prototype');
        $prototype->load(['model = ?', $model]);

        switch ($market) {
            case 'AMUS':
                $stores = ['AHUS', 'AHUS-FBA', 'ACUS', 'ACUS-FBA'];
                break;
            case 'AMEU ':
                $stores = ['AOUK', 'AODE', 'AODE-FBA', 'AKUK', 'AKEU', 'AKEU-FBA'];
                break;
            case 'ALI':
                $stores = ['ALI', 'ALI-FBA'];
                break;
        }

        $days = ceil(((strtotime($end) - strtotime($start)) / (24 * 3600)));

        $data = [];
        $chain = [];
        if (isset($stores)) {
            $channelFilter = " in ('" . implode("','", $stores) . "')";

            //data query
            $sql = sprintf('SELECT channel, size, count(*) as count FROM order_item WHERE prototype_id = %d AND create_time > ? AND create_time < ? AND channel %s GROUP by channel, size', $prototype['ID'], $channelFilter);
            $query = $db->exec($sql, [$start, $end]);
            foreach ($query as $item) {
                $size = $item['size'];
                if (empty($data[$size])) {
                    $data[$size] = [$item['channel'] => ['count' => $item['count']]];
                } else {
                    $data[$size][$item['channel']] = ['count' => $item['count']];
                }
            }

            //chain query
            $start = date('Y-m-d H:i:s', strtotime("$start - $days days"));
            $end = date('Y-m-d H:i:s', strtotime("$end - $days days"));
            $sql = sprintf('SELECT channel, size, count(*) as count FROM order_item WHERE prototype_id = %d AND create_time > ? AND create_time < ? AND channel %s GROUP by channel, size', $prototype['ID'], $channelFilter);
            $query = $db->exec($sql, [$start, $end]);
            foreach ($query as $item) {
                $size = $item['size'];
                if (empty($data[$size])) {
                    $chain[$size] = [$item['channel'] => ['count' => $item['count']]];
                } else {
                    $chain[$size][$item['channel']] = ['count' => $item['count']];
                }
            }
        } else {
            $stores = [$market];
            //data query
            $sql = sprintf('SELECT size, count(*) as count FROM order_item WHERE prototype_id = %d AND create_time > ? AND create_time < ? GROUP by size', $prototype['ID']);
            $query = $db->exec($sql, [$start, $end]);
            foreach ($query as $item) {
                $data[$item['size']] = [$market => ['count' => $item['count']]];
            }

            //chain query
            $start = date('Y-m-d H:i:s', strtotime("$start - $days days"));
            $end = date('Y-m-d H:i:s', strtotime("$end - $days days"));
            $sql = sprintf('SELECT size, count(*) as count FROM order_item WHERE prototype_id = %d AND create_time > ? AND create_time < ? GROUP by size', $prototype['ID']);
            $query = $db->exec($sql, [$start, $end]);
            foreach ($query as $item) {
                $chain[$item['size']] = [$market => ['count' => $item['count']]];
            }
        }
        $stock = new Mapper($db, 'stock');
        foreach ($data as $size => &$sizeStats) {
            foreach ($sizeStats as $channel => &$channelStats) {
                if ($chain[$size] && $chain[$size][$channel] && $chain[$size][$channel]['count']) {
                    $prev = $chain[$size][$channel]['count'];
                    $current = $channelStats['count'];
                    $channelStats['ratio'] = ($current - $prev) / $prev;
                } else {
                    $channelStats['ratio'] = '-';
                }
            }
            $sizeStats['cn'] = $stock->count(["prototype_id =? AND location = '中国' AND (size = ? OR size like ? OR size like ?)", $prototype['ID'], $size, $size . '=%', '%=' . $size]);
            $sizeStats['us'] = $stock->count(["prototype_id =? AND location = '美国' AND (size = ? OR size like ? OR size like ?)", $prototype['ID'], $size, $size . '=%', '%=' . $size]);
            $sizeStats['de'] = $stock->count(["prototype_id =? AND location = '德国' AND (size = ? OR size like ? OR size like ?)", $prototype['ID'], $size, $size . '=%', '%=' . $size]);
        }
        trace($db->log());
        ksort($data);
        return [
            'head' => array_merge($params, ['stores' => $stores]),
            'body' => $data
        ];
    }
}