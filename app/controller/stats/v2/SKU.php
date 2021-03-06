<?php

namespace controller\stats\v2;

use controller\stats\Base;
use DB\SQL\Mapper;
use db\SqlMapper;

class SKU extends Base
{
    private static $SIZE_US = ['US5', 'US6', 'US7', 'US8', 'US9', 'US9.5', 'US10', 'US11', 'US12', 'US13', 'US14', 'US15'];
    private static $SIZE_UK = ['UK2', 'UK3', 'UK4', 'UK5', 'UK6', 'UK7', 'UK8', 'UK9', 'UK10', 'UK11', 'UK12', 'UK13'];
    private static $SIZE_DE = ['EU35', 'EU36', 'EU37', 'EU38', 'EU39', 'EU40', 'EU41', 'EU42', 'EU43', 'EU44', 'EU45', 'EU46'];
    private static $AMUS = ['ADUS', 'ADUS-FBA', 'AEUS', 'AEUS-FBA', 'AIUS', 'AIUS-FBA', 'ALUS', 'ALUS-FBA', 'ASUS', 'ASUS-FBA', 'SHUS', 'SHUS-FBA'];
    private static $AMUK = ['AKUK', 'AKUK-FBA', 'REUK', 'REUK-FBA'];
    private static $AMDE = ['AEDE', 'AEDE-FBA', 'AIDE', 'AIDE-FBA', 'AKDE', 'AKDE-FBA', 'ARDE', 'ARDE-FBA'];
    private static $Zalando = ['Zalando', 'Zalando-FBA'];

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
            $error['message'] = 'SKU [' . $model . '] NOT FOUND';
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
        if (($end - $start) > $this->maxStatsSeconds) {
            $error['code'] = -1;
            $error['message'] = "起始时间与终止时间不能大于 $this->maxStatsDays 天";
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
                $stores = self::$AMUS;
                $allSize = self::$SIZE_US;
                break;
            case 'AMUK':
                $stores = self::$AMUK;
                $allSize = self::$SIZE_UK;
                break;
            case 'AMDE':
                $stores = self::$AMDE;
                $allSize = self::$SIZE_DE;
                break;
            case 'ZALANDO':
                $stores = self::$Zalando;
                $allSize = self::$SIZE_DE;
                break;
            case 'ALI':
                $stores = ['ALI', 'ALI-FBA'];
                $allSize = self::$SIZE_US;
                break;
            default:
                $allSize = array_merge(self::$SIZE_US, self::$SIZE_UK, self::$SIZE_DE);
        }

        $days = ceil(((strtotime($end) - strtotime($start)) / (24 * 3600)));

        $data = [];
        $chain = [];
        $soldSize = [];
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
            $soldSize = array_column($query, 'size');
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
            $soldSize = array_column($query, 'size');
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
                    $channelStats['ratio'] = sprintf('同比：%.2f', ($current - $prev) / $prev);
                } else {
                    $channelStats['ratio'] = '';
                }
            }
            $sizeStats['cn'] = $stock->count(["prototype_id =? AND location = '中国' AND (size = ? OR size like ? OR size like ?)", $prototype['ID'], $size, $size . '=%', '%=' . $size]);
            $sizeStats['us'] = $stock->count(["prototype_id =? AND location = '美国' AND (size = ? OR size like ? OR size like ?)", $prototype['ID'], $size, $size . '=%', '%=' . $size]);
            $sizeStats['de'] = $stock->count(["prototype_id =? AND location LIKE '德国%' AND (size = ? OR size like ? OR size like ?)", $prototype['ID'], $size, $size . '=%', '%=' . $size]);
            $sizeStats['uk'] = $stock->count(["prototype_id =? AND location = '英国' AND (size = ? OR size like ? OR size like ?)", $prototype['ID'], $size, $size . '=%', '%=' . $size]);
        }
        $unsoldSize = array_diff($allSize, $soldSize);
        foreach ($unsoldSize as $size) {
            $data[$size] = [
                'cn' => $stock->count(["prototype_id =? AND location = '中国' AND (size = ? OR size like ? OR size like ?)", $prototype['ID'], $size, $size . '=%', '%=' . $size]),
                'us' => $stock->count(["prototype_id =? AND location = '美国' AND (size = ? OR size like ? OR size like ?)", $prototype['ID'], $size, $size . '=%', '%=' . $size]),
                'de' => $stock->count(["prototype_id =? AND location LIKE '德国%' AND (size = ? OR size like ? OR size like ?)", $prototype['ID'], $size, $size . '=%', '%=' . $size]),
                'uk' => $stock->count(["prototype_id =? AND location = '英国' AND (size = ? OR size like ? OR size like ?)", $prototype['ID'], $size, $size . '=%', '%=' . $size]),
            ];
        }
        uksort($data, function ($a, $b) {
            $c1 = substr($a, 0, 2);
            $c2 = substr($b, 0, 2);
            if ($c1 == $c2) {
                $s1 = substr($a, 2);
                $s2 = substr($b, 2);
                if ($s1 == $s2) {
                    return 0;
                } else {
                    return $s1 < $s2 ? -1 : 1;
                }
            } else {
                return $c1 < $c2 ? -1 : 1;
            }
        });
        return [
            'head' => array_merge($params, ['stores' => $stores]),
            'body' => $data
        ];
    }

    function download($f3)
    {
        $startDate = date('Y-m-d', strtotime("last Saturday"));
        $endDate = date('Y-m-d', strtotime("Friday"));
        $dir = $f3->TEMP . 'downloads/';
        if (!is_dir($dir)) {
            mkdir($dir, 0700, true);
        }
        $file = $dir . "sku-stats-$startDate-$endDate.csv";
        if (!is_file($file) || filemtime($file) < strtotime($endDate . " 23:59:59")) {
            if (is_file($file)) {
                unlink($file);
            }
            $time = time();
            $stores = array_merge(self::$AMUS, self::$AMUK, self::$AMDE, self::$Zalando);
            $channels = implode("','", $stores);
            $db = SqlMapper::getDbEngine();
            $sql = "SELECT distinct(p.model) FROM order_item o, prototype p WHERE o.create_time>'$startDate' AND o.create_time<'$endDate 23:59:59' AND channel IN ('$channels') AND o.prototype_id=p.ID ORDER BY 1";
            $sku = $db->exec($sql);
            trace("sku stats download: $startDate - $endDate with " . count($sku) . " sku");
            $tmpFile = "$file-$time";
            $csv = fopen($tmpFile, "a");
            fputcsv($csv, array_merge(["sku"], $stores, ["CN","US","DE","UK"]));
            try {
                $prototype = new SqlMapper('prototype');
                $stock = new Mapper($db, 'stock');
                foreach ($sku as $item) {
                    $prototype->reset();
                    $prototype->loadOne(['model=?', $item['model']]);
                    $pid = $prototype['ID'];
                    $sql = "SELECT channel, count(*) as count FROM order_item WHERE prototype_id=$pid AND create_time>'$startDate' AND create_time<'$endDate 23:59:59' AND channel IN ('$channels') GROUP by channel";
                    $query = $db->exec($sql);
                    $channelStat = array_flip($stores);
                    foreach ($channelStat as $channel => $value) {
                        $channelStat[$channel] = 0;
                    }
                    foreach ($query as $channelCount) {
                        $channelStat[$channelCount['channel']] = $channelCount['count'];
                    }
                    $stock->reset();
                    $stockStat = [
                        'cn' => $stock->count(["prototype_id=? AND location='中国'", $prototype['ID']]),
                        'us' => $stock->count(["prototype_id=? AND location='美国'", $prototype['ID']]),
                        'de' => $stock->count(["prototype_id=? AND location LIKE '德国%'", $prototype['ID']]),
                        'uk' => $stock->count(["prototype_id=? AND location='英国'", $prototype['ID']]),
                    ];
                    fputcsv($csv, array_merge([$item['model']], $channelStat, $stockStat));
                    unset($query);
                    unset($channelStat);
                    unset($stockStat);
                }
            } catch (\Exception $e) {
                var_dump($e);
            }
            fclose($csv);
            rename($tmpFile, $file);
            trace("sku status download execution time: " . (time() - $time) . "s");
        }
        header('Cache-Control: no-cache');
        \Web::instance()->send($file);
    }
}
