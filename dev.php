<?php


return [
    // 服务名称
    'SERVER_NAME'   => "EasySwoole",
    'MAIN_SERVER'   => [
        // 监听地址
        'LISTEN_ADDRESS' => '0.0.0.0',
        // 监听端口
        'PORT'           => 9501,
        // 可选为 EASYSWOOLE_SERVER|EASYSWOOLE_WEB_SERVER|EASYSWOOLE_WEB_SOCKET_SERVER
        'SERVER_TYPE'    => EASYSWOOLE_WEB_SERVER,
        // 该配置项当为 SERVER_TYPE 值为 TYPE_SERVER 时有效
        'SOCK_TYPE'      => SWOOLE_TCP,
        // 默认 Server 运行模式
        'RUN_MODEL'      => SWOOLE_PROCESS,
        // Swoole_Server 运行配置（ 完整配置可见[Swoole文档](https://wiki.swoole.com/wiki/page/274.html) ）
        'SETTING'        => [
            // 运行的 worker 进程数量
            'worker_num'            => 8,
            // 设置异步重启开关。设置为true时，将启用异步安全重启特性，Worker进程会等待异步事件完成后再退出。
            'reload_async'          => true,
            // 开启后自动在 onTask 回调中创建协程
            'task_enable_coroutine' => true,
            'max_wait_time'         => 3
        ],
        'TASK'=>[
            'workerNum'     => 4,
            'maxRunningNum' => 128,
            'timeout'       => 15
        ]
    ],
    // 临时文件存放的目录
    'TEMP_DIR'      => null,
    // 日志文件存放的目录
    'LOG_DIR'       => null,

    // 数据库配置文件
    'MYSQL'  => [
        'host'          => '127.0.0.1',
        'port'          => 3306,
        'user'          => 'root',
        'password'      => '123456',
        'database'      => 'tongji',
        'timeout'       => 5,
        'charset'       => 'utf8mb4',
    ],
    // 分表数量控制
    'MYSQL_LIMIT_NUM' => 10,
    // REDIS配置文件
    'REDIS'  => [
        'host'          => '127.0.0.1',
        'port'          => 6379,
        'timeout'       => 3,
    ],
    // jwt 配置
    'JWT' => [
        //token在header中的name
        'name'                   => 'token',
        //token加密使用的secret
        'secret'                 => '552ac90778a976c72f7f673db174df30',
        //颁发者
        'iss'                    => 'iss',
        //使用者
        'aud'                    => 'aud',
        //过期时间，以秒为单位，默认2小时。提示：感觉刷新麻烦的可以设置的大一些，比如10年20年之类的
        'ttl'                    => 7200,
        //刷新时间，以秒为单位，默认14天。提示：只有过期时间到了才会生效，所以把过期时间设置的很大的懒人就可以忽略了
        'refresh_ttl'            => 1209600,
        //是否自动刷新，开启后可自动刷新token，附在header中返回，name为`Authorization`,字段为`Bearer `+$token
        'auto_refresh'           => true,
        //黑名单宽限期，以秒为单位，首次token刷新之后在此时间内原token可以继续访问
        'blacklist_grace_period' => 60
    ],
];