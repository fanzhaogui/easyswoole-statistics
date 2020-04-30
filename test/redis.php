<?php
/**
 * Created by PhpStorm.
 * User: fanzhaogui
 * Date: 2020/4/28
 * Time: 16:24
 */

// redis协程客户端
// https://www.easyswoole.com/Cn/Components/Redis/install.html
// composer require easyswoole/redis

require_once '../vendor/autoload.php';

go(function () {
    $config = [
        'host' => '127.0.0.1',
        'port' => 6379,
        'auth' => '',
        'serialize' => \EasySwoole\Redis\Config\RedisConfig::SERIALIZE_NONE
    ];
    $redis = new \EasySwoole\Redis\Redis(new \EasySwoole\Redis\Config\RedisConfig($config));

    $cacheKey = 'a';
    var_dump($redis->set($cacheKey, 1));
    var_dump($redis->get($cacheKey));
});