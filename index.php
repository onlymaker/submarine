<?php
/**
 * Created by PhpStorm.
 * User: jibo
 * Date: 16/3/12
 * Time: 10:06
 */
define('ROOT', __DIR__);

require_once 'vendor/autoload.php';

$f3 = Base::instance();

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

$smarty = new Smarty();
$smarty->addTemplateDir('app/tpl');
$smarty->setCompileDir($f3->get('SMARTY_COMPILE'));
$smarty->setCacheDir($f3->get('SMARTY_COMPILE'));
$smarty->compile_locking = false;
$smarty->left_delimiter  = '{{';
$smarty->right_delimiter = '}}';
$smarty->escape_html = true;
$smarty->setCaching(Smarty::CACHING_OFF);
$smarty->setCacheLifetime(0);

function trace($log) {
    $logger = new Log(date('Y-m-d').'.log');
    $logger->write($log, 'Y-m-d H:i:s');
}

$f3->run();
