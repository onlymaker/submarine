<?php

namespace controller\stats\v2;

use controller\stats\Base;
use DB\SQL\Mapper;
use db\SqlMapper;

class ASIN extends Base
{
    function stats($f3)
    {
        global $smarty;
        $smarty->assign('title', 'ASIN - stats');
        if ($f3->VERB == 'POST') {
            $parentAsin = strtoupper(preg_replace(['/^\s*/', '/\s*$/'], '', $_POST['asin']));
            $start = $_POST['start-date'];
            $end = $_POST['end-date'] . ' 23:59:59';
            $smarty->assign('data', $this->query($parentAsin, $start, $end));
            $smarty->display('stats/v2/asin_result.tpl');
        } else {
            $smarty->display('stats/v2/asin.tpl');
        }
    }

    function validate($f3)
    {
        $error = ['code' => 0];
        $db = SqlMapper::getDbEngine();
        $parentAsin = $_POST['asin'];
        $asin = new Mapper($db, 'asin');
        $asin->load(['parent_asin = ?', $parentAsin]);
        if ($asin->dry()) {
            $error['code'] = -1;
            $error['message'] = 'ASIN [' . $parentAsin . '] NOT FOUND';
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

    function query($parentAsin, $start, $end)
    {
        ini_set('max_execution_time', 600);
        $days = ceil(((strtotime($end) - strtotime($start)) / (24 * 3600)));
        $chainStart = date('Y-m-d H:i:s', strtotime("$start - $days days"));
        $chainEnd = date('Y-m-d H:i:s', strtotime("$end - $days days"));

        $db = SqlMapper::getDbEngine();
        $asin = new Mapper($db, 'asin');
        $models = $asin->find(['parent_asin = ?', $parentAsin], ['order' => 'model']);
        $channels = array_unique(array_column($models, 'store'));

        $skuStats = [];
        foreach ($models as $model) {
            $sku = $model['model'];
            $skuStats[$sku] = ['channel'];
            $skuStats[$sku]['channel'] = [];
            foreach ($channels as $channel) {
                $fbaChannel = $channel . '-FBA';
                $sql = sprintf("SELECT count(*) as count FROM order_item o, prototype p WHERE p.model = '%s' AND p.ID = o.prototype_id AND o.channel = '%s' AND o.create_time > '%s' AND o.create_time < '%s'", $sku, $channel, $start, $end);
                list($result) = $db->exec($sql);
                $sql = sprintf("SELECT count(*) as count FROM order_item o, prototype p WHERE p.model = '%s' AND p.ID = o.prototype_id AND o.channel = '%s' AND o.create_time > '%s' AND o.create_time < '%s'", $sku, $channel, $chainStart, $chainEnd);
                list($chainResult) = $db->exec($sql);
                $sql = sprintf("SELECT count(*) as count FROM order_item o, prototype p WHERE p.model = '%s' AND p.ID = o.prototype_id AND o.channel = '%s' AND o.create_time > '%s' AND o.create_time < '%s'", $sku, $fbaChannel, $start, $end);
                list($fbaResult) = $db->exec($sql);
                $sql = sprintf("SELECT count(*) as count FROM order_item o, prototype p WHERE p.model = '%s' AND p.ID = o.prototype_id AND o.channel = '%s' AND o.create_time > '%s' AND o.create_time < '%s'", $sku, $fbaChannel, $chainStart, $chainEnd);
                list($fbaChainResult) = $db->exec($sql);
                $skuStats[$sku]['channel'][$channel] = [
                    'count' => $result['count'],
                    'ratio' => $result['count'] && $chainResult['count'] ? sprintf('%.2f%%', ($result['count'] - $chainResult['count']) / $chainResult['count'] * 100) : '-',
                    'fbaCount' => $fbaResult['count'],
                    'fbaRatio' => $fbaResult['count'] && $fbaChainResult['count'] ? sprintf('%.2f%%', ($fbaResult['count'] - $fbaChainResult['count']) / $fbaChainResult['count'] * 100) : '-',
                ];
            }

            $sql = sprintf("SELECT count(*) as count FROM stock s, prototype p WHERE s.location='中国' AND s.prototype_id=p.ID AND p.model='%s'", $sku);
            list($stockInChina) = $db->exec($sql);
            $skuStats[$sku]['china'] = $stockInChina['count'];

            $sql = sprintf("SELECT count(*) as count FROM stock s, prototype p WHERE s.location='美国' AND s.prototype_id=p.ID AND p.model='%s'", $sku);
            list($stockInAmerica) = $db->exec($sql);
            $skuStats[$sku]['america'] = $stockInAmerica['count'];

            $sql = sprintf("SELECT count(*) as count FROM stock s, prototype p WHERE s.location='德国' AND s.prototype_id=p.ID AND p.model='%s'", $sku);
            list($stockInGerman) = $db->exec($sql);
            $skuStats[$sku]['german'] = $stockInGerman['count'];

            $sql = sprintf("SELECT count(*) as count FROM stock s, prototype p WHERE s.location='英国' AND s.prototype_id=p.ID AND p.model='%s'", $sku);
            list($stockInEngland) = $db->exec($sql);
            $skuStats[$sku]['england'] = $stockInEngland['count'];
        }

        $modelOptions = implode("','", \Matrix::instance()->pick($models, 'model'));
        $modelOptions = "'" . $modelOptions . "'";
        $sizes = $db->exec('SELECT DISTINCT(size) as size FROM order_item o, prototype p WHERE p.model in (' . $modelOptions . ') AND o.prototype_id = p.ID AND o.channel in (?, ?) AND o.create_time > ? AND o.create_time < ? ORDER BY 1', [$channel, $fbaChannel, $start, $end]);
        $sizes = \Matrix::instance()->pick($sizes, 'size');
        $flipSizes = array_flip($sizes);
        foreach ($flipSizes as $key => &$value) {
            $value = preg_replace('/^\D*/', '', $key);
        }
        $sizes = array_flip($flipSizes);
        ksort($sizes);

        $sizeStats = [];
        foreach ($sizes as $size) {
            $sizeStats[$size] = ['channel'];
            $sizeStats[$size]['channel'] = [];
            foreach ($channels as $channel) {
                $sql = sprintf("SELECT count(*) as count FROM order_item o, prototype p WHERE p.model in (%s) AND p.ID = o.prototype_id AND (o.size = '%s' OR o.size LIKE '%s=%%' OR o.size LIKE '%%=%s') AND o.channel = '%s' AND o.create_time > '%s' AND o.create_time < '%s'", $modelOptions, $size, $size, $size, $channel, $start, $end);
                list($result) = $db->exec($sql);
                $sql = sprintf("SELECT count(*) as count FROM order_item o, prototype p WHERE p.model in (%s) AND p.ID = o.prototype_id AND (o.size = '%s' OR o.size LIKE '%s=%%' OR o.size LIKE '%%=%s') AND o.channel = '%s' AND o.create_time > '%s' AND o.create_time < '%s'", $modelOptions, $size, $size, $size, $channel, $chainStart, $chainEnd);
                list($chainResult) = $db->exec($sql);
                $sql = sprintf("SELECT count(*) as count FROM order_item o, prototype p WHERE p.model in (%s) AND p.ID = o.prototype_id AND (o.size = '%s' OR o.size LIKE '%s=%%' OR o.size LIKE '%%=%s') AND o.channel = '%s' AND o.create_time > '%s' AND o.create_time < '%s'", $modelOptions, $size, $size, $size, $fbaChannel, $start, $end);
                list($fbaResult) = $db->exec($sql);
                $sql = sprintf("SELECT count(*) as count FROM order_item o, prototype p WHERE p.model in (%s) AND p.ID = o.prototype_id AND (o.size = '%s' OR o.size LIKE '%s=%%' OR o.size LIKE '%%=%s') AND o.channel = '%s' AND o.create_time > '%s' AND o.create_time < '%s'", $modelOptions, $size, $size, $size, $fbaChannel, $chainStart, $chainEnd);
                list($fbaChainResult) = $db->exec($sql);
                $sizeStats[$size]['channel'][$channel] = [
                    'count' => $result['count'],
                    'ratio' => $result['count'] && $chainResult['count'] ? sprintf('%.2f%%', ($result['count'] - $chainResult['count']) / $chainResult['count'] * 100) : '-',
                    'fbaCount' => $fbaResult['count'],
                    'fbaRatio' => $fbaResult['count'] && $fbaChainResult['count'] ? sprintf('%.2f%%', ($fbaResult['count'] - $fbaChainResult['count']) / $fbaChainResult['count'] * 100) : '-'
                ];
            }
        }

        return [
            'head' => [
                'asin' => $parentAsin,
                'channels' => $channels,
                'start' => $start,
                'end' => substr($end, 0, 10)
            ],
            'sku' => $skuStats,
            'size' => $sizeStats
        ];
    }
}
