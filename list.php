<?php

if (PHP_SAPI != 'cli') {
    error('Not allowed in ' . PHP_SAPI);
}

require_once __DIR__ . '/vendor/autoload.php';

$f3 = Base::instance();

$f3->config(__DIR__ . '/config/env.cfg');

$file = '/tmp/thumb';
$loop = 0;

$prefix = '';
$marker = '';
$limit = 1000;

$bucket = $f3->get('QINIU_BUCKET');

$manager = new \Qiniu\Storage\BucketManager(new \Qiniu\Auth($f3->get('QINIU_ACCESS_KEY'), $f3->get('QINIU_SECRET_KEY')));

echo 'Start', PHP_EOL;

while (!$loop || !empty($marker)) {
    list($results, $marker, $error) = $manager->listFiles($bucket, $prefix, $marker, $limit);
    empty($error) ? append($file, $results) : error($error);
    $loop ++;
    echo $loop, ':', $marker, PHP_EOL;
}

echo 'Success', PHP_EOL;

function append($file, $results)
{
    foreach($results as $result) {
        if (strstr($result['key'], 'thumb') !== false) {
            file_put_contents($file, $result['key'] . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
    }
}

function error($error)
{
    ob_start();
    var_dump($error);
    die(ob_get_clean());
}
