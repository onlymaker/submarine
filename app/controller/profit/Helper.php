<?php
/**
 * Created by PhpStorm.
 * User: wangd
 * Date: 2016/3/19
 * Time: 10:15
 */

namespace controller\profit;

class Helper {
    function statsWeekOrMonth($year, $target='week') {

        if($target == 'quarter') {
            $meta = array(
                'full' => 'Quarter',
                'short' =>  'Q',
                'chinese' => '季度'
            );
            $fields = 'sum(profit) as profit, quarter(price_time) as i';
        } else if($target == 'month') {
            $meta = array(
                'full' => 'Month',
                'short' =>  'M',
                'chinese' => '月'
            );
            $fields = 'sum(profit) as profit, month(price_time) as i';
        } else {
            $meta = array(
                'full' => 'Week',
                'short' =>  'W',
                'chinese' => '周'
            );
            $fields = 'sum(profit) as profit, weekofyear(price_time) as i';
        }

        $pdo = new \db\Base();
        $stats = $pdo->_fetchArray(
            'finance_entry',
            $fields,
            array(array('year(price_time)=?', $year)),
            array('group' => 'i', 'order' => 'i'),
            0, 0, 0
        );
        foreach($stats as $idx=>&$item) {
            if($idx == 0) {
                if($prev['amount'] == 0) $item['amountRatio'] = 'undefined';
                else $item['amountRatio'] = sprintf('%.2f%%', ($item['amount'] - $prev['amount']) / $prev['amount']);
                if($prev['quantity'] == 0) $item['quantityRatio'] = 'undefined';
                else $item['quantityRatio'] = sprintf('%.2f%%', ($item['quantity'] - $prev['quantity']) / $prev['quantity']);
            } else {
                if($stats[$idx-1]['amount'] == 0) $item['amountRatio'] = 'undefined';
                else $item['amountRatio'] = sprintf('%.2f%%', ($item['amount'] - $stats[$idx-1]['amount'])/$stats[$idx-1]['amount']);
                if($stats[$idx-1]['quantity'] == 0) $item['quantityRatio'] = 'undefined';
                else $item['quantityRatio'] = sprintf('%.2f%%', ($item['quantity'] - $stats[$idx-1]['quantity'])/$stats[$idx-1]['quantity']);
            }
        }

        global $smarty;
        $smarty->assign('title', '利润统计');
        $smarty->assign('meta', $meta);
        $smarty->assign('year', $year);
        $smarty->assign('stats', $stats);
        $smarty->display('profit/stats.tpl');
    }
}