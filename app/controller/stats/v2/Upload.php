<?php

namespace controller\stats\v2;

use controller\stats\Base;
use DB\SQL\Mapper;
use db\SqlMapper;

class Upload extends Base
{
    static $fields = [
        'product-meta' => ['model', 'color', 'heel'],
        'product-asin' => ['model', 'parent_asin', 'child_asin', 'store']
    ];

    function get()
    {
        global $f3, $smarty;
        $smarty->assign('title', 'Upload');
        $smarty->display('stats/v2/upload.tpl');
    }

    function post()
    {
        global $f3;

        $dir = $f3->get('UPLOAD_DIR'). 'excel/';

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $type = $_POST['type'];
        $suffix = end(explode('.', $_FILES['file']['name']));
        $file = $dir . $type . date('_Ymd.') . $suffix;
        rename($_FILES['file']['tmp_name'], $file);

        echo json_encode($this->parse($file, $type), JSON_UNESCAPED_UNICODE);
    }

    function parse($file, $type)
    {
        try {
            @ini_set('memory_limit', '256M');
            $excel = \PHPExcel_IOFactory::load($file);
            $sheet = $excel->getSheet(0);
            $rows = $sheet->toArray();
            switch ($type) {
                case 'product-meta':
                    return $this->processMeta($rows);
                case 'product-asin':
                    return $this->processAsin($rows);
                default:
                    return ['error' => [
                        'code' => -1,
                        'text' => 'Unknown type: ' . $type,
                    ]];
            }
        }  catch (\Exception $exception) {
            return ['error' => [
                'code' => $exception->getCode(),
                'text' => $exception->getMessage(),
                'trace' => $exception->getTrace(),
            ]];
        }
    }

    function processMeta($data)
    {
        $db = SqlMapper::getDbEngine();
        $db->begin();
        $asin = new Mapper($db, 'asin');
        $prototype = new Mapper($db, 'prototype');
        $result = [];
        $names = array_flip(self::$fields['product-meta']);
        foreach ($data as $r => $row) {
            $model = preg_replace('/\s/', '', $row[$names['model']]);
            $prototype->load(['model = ?', $model]);
            if ($prototype->dry()) {
                $result[] = [
                    'row' => $r,
                    'error' => 'model ' . $model . ' not existed'
                ];
            } else {
                $attribute = [];
                unset($names['model']);
                foreach ($names as $name => $index) {
                    $attribute[$name] = $row[$index];
                }
                $json = json_encode($attribute);
                if ($json) {
                    $prototype['attribute'] = $json;
                    $prototype->save();
                } else {
                    $result[] = [
                        'row' => $r,
                        'error' => json_last_error_msg()
                    ];
                }
            }
        }
        $db->commit();
        return [
            'error' => ['code' => 0],
            'result' => $result
        ];
    }

    function processAsin($data)
    {
        $db = SqlMapper::getDbEngine();
        $db->begin();
        $mapper = new Mapper($db, 'asin');
        $result = [];
        $fields = self::$fields['product-asin'];
        foreach ($data as $r => $row) {
            $tmp = [];
            foreach ($row as $key => $value) {
                if ($fields[$key]) {
                    $tmp[$fields[$key]] = preg_replace('/\s/', '', $value);
                }
            }
            $mapper->load(['model = ? AND parent_asin = ? AND child_asin = ?', $tmp['model'], $tmp['parent_asin'], $tmp['child_asin']]);
            if ($mapper->dry()) {
                foreach ($tmp as $key => $value) {
                    $mapper[$key] = $value;
                }
                $mapper->save();
            } else {
                $result[] = [
                    'row' => $r,
                    'error' => 'already exists'
                ];
            }
        }
        $db->commit();
        return [
            'error' => ['code' => 0],
            'result' => $result
        ];
    }
}
