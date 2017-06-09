<?php

if (PHP_SAPI != 'cli') {
    error('Not allowed in ' . PHP_SAPI);
}

require_once __DIR__ . '/vendor/autoload.php';

$file = '/tmp/thumb';

$f3 = Base::instance();

$f3->config(__DIR__ . '/config/env.cfg');

$bucket = $f3->get('QINIU_BUCKET');

$manager = new \Qiniu\Storage\BucketManager(new \Qiniu\Auth($f3->get('QINIU_ACCESS_KEY'), $f3->get('QINIU_SECRET_KEY')));

if ($argc == 1) {// list qiniu thumb
    $loop = 0;
    $prefix = '';
    $marker = '';
    $limit = 1000;

    if (is_file($file)) {
        unlink($file);
    }

    echo 'Start', PHP_EOL;

    while (!$loop || !empty($marker)) {
        list($results, $marker, $error) = $manager->listFiles($bucket, $prefix, $marker, $limit);
        empty($error) ? append($file, $results) : error($error);
        $loop ++;
        echo $loop, ':', $marker, PHP_EOL;
    }

    echo 'Success', PHP_EOL;
} else {// delete thumb file
    if (is_file($file)) {
        $content = file_get_contents($file);
        $lines = explode(PHP_EOL, $content);
        foreach ($lines as $line) {
            echo 'delete:', $line, PHP_EOL;
            $manager->delete($bucket, $line);
        }
    }
}

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
