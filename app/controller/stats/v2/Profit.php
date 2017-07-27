<?php

namespace controller\stats\v2;

use controller\stats\Base;
use DB\SQL\Mapper;
use db\SqlMapper;

class Profit extends Base
{
    function stats($f3)
    {
        global $smarty;
        $smarty->assign('title', 'Profit - stats');
        if ($f3->VERB == 'POST') {
            $day = date('j');
            if ($day < 15) {
                $start = date('Y-m-01', strtotime('- 4 month'));
                $end = date('Y-m-01', strtotime('- 1 month'));
            } else {
                $start = date('Y-m-01', strtotime('- 3 month'));
                $end = date('Y-m-01');
            }
            $data = [];
            $model = $_POST['sku'];
            $db = SqlMapper::getDbEngine();
            $prototype = new Mapper($db, 'prototype');
            $prototype->load(['model = ?', $model]);
            if (!$prototype->dry()) {
                $sql = "SELECT o.size, o.channel, count(*) as count, sum(f.price) as price, sum(f.profit) as profit, sum(f.express) as express, sum(f.cost) as cost, sum(f.cut) as cut FROM finance_entry f, order_item o WHERE o.prototype_id = ? AND o.create_time > ? AND o.create_time < ? AND (o.size LIKE 'US%' OR o.size LIKE 'EU%') AND f.order_item_id = o.ID GROUP BY o.size, o.channel";
                $query = $db->exec($sql, [$prototype['ID'], $start, $end]);
                foreach ($query as $item) {
                    if ($item['count']) {
                        $data[$item['size']] = [
                            'channel' => $item['channel'],
                            'averagePrice' => $item['price'] / $item['count'],
                            'averageProfit' =>  $item['profit'] / $item['count'],
                            'costRatio' => sprintf('%.2f', $item['cost'] / ($item['price'] ?? $item['cost'])),
                            'expressRatio' => sprintf('%.2f', $item['express'] / ($item['price'] ?? $item['express'])),
                            'count' => $item['count'],
                            'return' => 0
                        ];
                        if ($item['cut']) {
                            list($return) = $db->exec('SELECT count(*) as count FROM order_item o, finance_entry f WHERE o.prototype_id = ? AND o.size = ? AND o.channel = ? AND o.create_time > ? AND o.create_time < ? AND o.ID = f.order_item_id AND f.cut != 0', [$prototype['ID'], $item['size'], $item['channel'], $start, $end]);
                            $data[$item['size']]['return'] = $return['count'];
                        }
                    }
                }
            }
            ksort($data);
            $smarty->assign('data', [
                'head' => [
                    'model' => $model,
                    'start' => $start,
                    'end' => $end
                ],
                'body' => $data
            ]);
            $smarty->display('stats/v2/profit_result.tpl');
        } else {
            $smarty->display('stats/v2/profit.tpl');
        }
    }

    function validate($f3)
    {
        $error = ['code' => 0];
        $model = $_POST['sku'];
        $db = SqlMapper::getDbEngine();
        $prototype = new Mapper($db, 'prototype');
        $prototype->load(['model = ?', $model]);
        if ($prototype->dry()) {
            $error['code'] = -1;
            $error['message'] = 'SKU NOT FOUND';
        }
        echo json_encode(['error' => $error], JSON_UNESCAPED_UNICODE);
    }
}
