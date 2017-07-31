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
        if (($end - $start) > (90 * 24 * 3600)) {
            $error['code'] = -1;
            $error['message'] = '起始时间与终止时间不能大于90天';
            echo json_encode(['error' => $error], JSON_UNESCAPED_UNICODE);
            return;
        }
        echo json_encode(['error' => $error], JSON_UNESCAPED_UNICODE);
    }
}
