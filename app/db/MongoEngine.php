<?php
/**
 * Created by PhpStorm.
 * User: Syncxplus
 * Date: 2016/1/25
 * Time: 9:31
 */

namespace db;

use DB\Mongo;

class MongoEngine extends Mongo{
    protected static $dbEngine;

    public static function getInstance()
    {
        if(null == static::$dbEngine) {
            global $f3;
            static::$dbEngine = new MongoEngine($f3->get("MONGO_DSN"), $f3->get("MONGO_DB"));
        }
        return static::$dbEngine;
    }

    function __construct($dsn,$dbname,array $options=NULL)
    {
        parent::__construct($dsn,$dbname,$options=NULL);
    }

    function log() {
        $log = parent::log();
        return str_replace("\n", "<br>", $log);
    }
}