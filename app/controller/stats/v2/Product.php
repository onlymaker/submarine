<?php

namespace controller\stats\v2;

use code\ProductMeta;
use controller\stats\Base;
use db\SqlMapper;
use utils\StringUtils;

class Product extends Base
{
    function stats($f3)
    {
        global $smarty;
        $smarty->assign('title', 'Product stats');
        if ($f3->VERB == 'POST') {
            $db = SqlMapper::getDbEngine();
            $attribute = $_POST['attribute'];
            $attrField = '$.' . StringUtils::camelToSnake($attribute);
            $start = $_POST['start-date'];
            $end = $_POST['end-date'];
            $sql = 'SELECT p.attribute->? as attribute, count(*) as count FROM prototype p, order_item o WHERE p.attribute is not null AND p.ID = o.prototype_id AND o.replacement = 0 AND o.create_time > ? AND o.create_time < ? GROUP by p.attribute->? ORDER BY 1';
            $query = $db->exec($sql, [$attrField, $start, $end, $attrField]);
            $data = [];
            foreach ($query as $item) {
                $data[$item['attribute']] = ['count' => $item['count']];
                $top = $db->exec('SELECT model, count(*) as count FROM prototype p, order_item o WHERE p.attribute->? = ? AND p.ID = o.prototype_id AND o.replacement = 0 AND o.create_time > ? AND o.create_time < ? GROUP BY model ORDER BY count DESC', [$attrField, str_replace('"', '', $item['attribute']), $start, $end]);
                foreach ($top as $key => $value) {
                    $data[$item['attribute']]['t' . $key] = $value['model'];
                }
            }

            $days = ceil(((strtotime($end) - strtotime($start)) / (24 * 3600)));
            $start = date('Y-m-d H:i:s', strtotime("$start - $days days"));
            $end = date('Y-m-d H:i:s', strtotime("$end - $days days"));
            $sql = 'SELECT p.attribute->? as attribute, count(*) as count FROM prototype p, order_item o WHERE p.attribute is not null AND p.ID = o.prototype_id AND o.replacement = 0 AND o.create_time > ? AND o.create_time < ? GROUP by p.attribute->? ORDER BY 1';
            $query = $db->exec($sql, [$attrField, $start, $end, $attrField]);
            $chain = [];
            foreach ($query as $item) {
                $chain[$item['attribute']] = ['count' => $item['count']];
            }

            list($total) = $db->exec('SELECT count(*) as count FROM prototype p, order_item o WHERE p.attribute is not null AND p.ID = o.prototype_id AND o.replacement = 0 AND o.create_time > ? AND o.create_time < ?', [$start, $end]);
            foreach ($data as $attr => &$attrStats) {
                $count = $attrStats['count'];
                $attrStats['ratio'] = sprintf('%.2f', $count / ($total['count'] ?? $count));
                if ($chain[$attr] && $chain[$attr]['count']) {
                    $prev = $chain[$attr]['count'];
                    $attrStats['chainRatio'] = sprintf('%.2f', ($count - $prev) / $prev);
                } else {
                    $attrStats['chainRatio'] = '-';
                }
            }
            $smarty->assign('data', [
                'head' => [
                    'attribute' => $attribute,
                    'start' => $start,
                    'end' => $end
                ],
                'body' => $data
            ]);
            $smarty->display('stats/v2/product_result.tpl');
        } else {
            $smarty->assign('meta', ['color', 'heelHeight', 'heelType', 'occasion', 'structure', 'toe']);
            $smarty->display('stats/v2/product.tpl');
        }
    }

    function validate($f3)
    {
        $error = ['code' => 0];
        $attribute = $_POST['attribute'];
        if (!isset(ProductMeta::$$attribute)) {
            $error['code'] = -1;
            $error['message'] = 'ATTRIBUTE NOT FOUND';
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
}
