<?php

namespace controller\stats\v2;

use code\ProductMeta;
use controller\stats\Base;
use DB\SQL\Mapper;
use db\SqlMapper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Upload extends Base
{
    static $fields = [
        'product-meta' => ['model', 'color', 'heel_height', 'occasion', 'heel_type', 'toe', 'structure'],
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
            ini_set('max_execution_time', 600);
            ini_set('memory_limit', '256M');
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
            $model = preg_replace(['/^\s*/', '/\s*$/'], '', $row[$names['model']]);
            if (!empty($model)) {
                $prototype->load(['model = ?', $model]);
            } else {
                $prototype->reset();
            }
            if ($prototype->dry()) {
                $result[] = [
                    'row' => $r + 1,
                    'error' => 'model ' . $model . ' not existed'
                ];
            } else {
                $attribute = [];
                foreach ($names as $name => $index) {
                    if ($name != 'model') {
                        $value = strtolower(preg_replace(['/^\s*/', '/\s*$/'], '', $row[$index]));
                        if (ProductMeta::validate($name, $value)) {
                            $attribute[$name] = $value;
                        } else {
                            $result[] = [
                                'row' => $r + 1,
                                'error' => $model . ' ' . $name . ': ' . $value . ' is invalid'
                            ];
                        }
                    }
                }
                if (count($attribute) == count($names) - 1) { //model not in attribute
                    $json = json_encode($attribute);
                    if ($json) {
                        $prototype['attribute'] = $json;
                        $prototype->save();
                    } else {
                        $result[] = [
                            'row' => $r + 1,
                            'error' => json_last_error_msg()
                        ];
                    }
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
        $asin = new Mapper($db, 'asin');
        $prototype = new Mapper($db, 'prototype');
        $result = [];
        $names = array_flip(self::$fields['product-asin']);
        foreach ($data as $r => $row) {
            $model = strtoupper(preg_replace(['/^\s*/', '/\s*$/'], '', $row[$names['model']]));
            if (!empty($model)) {
                $prototype->load(['model = ?', $model]);
            } else {
                $prototype->reset();
            }
            if ($prototype->dry()) {
                $result[] = [
                    'row' => $r + 1,
                    'error' => 'model ' . $model . ' not existed'
                ];
            } else {
                $tmp = [];
                foreach ($names as $name => $index) {
                    $tmp[$name] = preg_replace(['/^\s*/', '/\s*$/'], '', $row[$index]);
                }
                $tmp['store'] = strtoupper($tmp['store']);
                //sku, parent_asin, store should be unique
                $asin->load(['model = ? AND parent_asin = ? AND store = ?', $tmp['model'], $tmp['parent_asin'], $tmp['store']]);
                if ($asin->dry()) {
                    foreach ($tmp as $key => $value) {
                        $asin[$key] = $value;
                    }
                    $asin->save();
                } else {
                    $result[] = [
                        'row' => $r + 1,
                        'error' => 'current row already existed'
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

    function template($f3) {
        $dir = $f3->TEMP . 'downloads/';
        if (!is_dir($dir)) {
            mkdir($dir, 0700, true);
        }
        $type = $_GET['type'];
        $template = $dir . 'template_' . $type . '.xlsx';
        if (!is_file($template)) {
            $spreadsheet = new SpreadSheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->fromArray(self::$fields[$type], '', 'A1');
            $writer = new Xlsx($spreadsheet);
            $writer->save($template);
        }
        \Web::instance()->send($template);
    }
}
