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
            $model = $_POST['sku'];
            $market = $_POST['market'];
            $start = $_POST['start'];
            $end = $_POST['end'];
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
}
