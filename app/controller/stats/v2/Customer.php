<?php

namespace controller\stats\v2;

use controller\stats\Base;
use db\SqlMapper;

class Customer extends Base
{
    static $MIN_CUSTOMER_TIMES = 5;

    function purchaseFrequency($f3)
    {
        @ini_set('max_execution_time', 600);
        @ini_set('memory_limit', '128M');

        $dir = $f3->TEMP . 'downloads/';
        if (!is_dir($dir)) {
            mkdir($dir, 0700, true);
        }
        $csv = 'customer_times_' . date('Y-m-d') . '.csv';

        header('Content-Type: octet-stream');
        header('Content-Disposition: attachment; filename="customer_times.csv"');

        $end = date('Y-m-d');
        $start = date('Y-m-d', strtotime("$end - 6 months"));
        $sql = "SELECT name, postcode, count(*) as count FROM distribution WHERE name != '' AND postcode != '' AND create_time > ? AND create_time < ? GROUP by name, postcode ORDER BY count DESC, name";
        $query = SqlMapper::getDbEngine()->exec($sql, [$start, $end]);

        $file = fopen($dir . $csv, 'a');
        foreach ($query as $item) {
            if ($item['count'] > self::$MIN_CUSTOMER_TIMES) {
                fputcsv($file, $item);
                flush();
                ob_flush();
            } else {
                break;
            }
        }
        fclose($file);

        echo file_get_contents($dir . $csv);
    }
}
