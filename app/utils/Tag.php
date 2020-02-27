<?php
/**
 * Created by PhpStorm.
 * User: jibo
 * Date: 2016/9/7
 * Time: 21:21
 */

namespace utils;

use db\SqlMapper;

class Tag {
    public static function getTags() {
        $query = SqlMapper::getDbEngine()->exec('select distinct name from tag order by weight desc');
        return array_column($query, 'name');
    }
}