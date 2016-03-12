<?php
/**
 * Created by PhpStorm.
 * User: jibo
 * Date: 16/3/12
 * Time: 10:06
 */

require_once 'vendor/autoload.php';

$f3 = require_once 'vendor/bcosca/fatfree-core/base.php';

$f3->config('config/common.cfg');
if(file_exists('config/env.cfg')) {
    $f3->config('config/env.cfg');
    if(file_exists('config/'.$f3->get('env').'.cfg')) {
        $f3->config('config/'.$f3->get('env').'.cfg');
    }
}

$f3->config('config/route.cfg');
$f3->config('config/map.cfg');

$f3->set('AUTOLOAD', __DIR__.'/app/');

$f3->run();

function trace($log) {
    $logger = new Log(date('Y-m-d').'.log');
    $logger->write($log, 'Y-m-d H:i:s');
}