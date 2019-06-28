<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/1
 * Time: 19:57
 */
// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------

return [
    'MONGO' =>[
        'type'          => '\think\mongo\Connection',
        // 设置查询类
        'query'			 => '\think\mongo\Query',
        'hostname'      => '127.0.0.1',
        'database'      => 'test',
        'username'      => 'app_p2p_mange',
        'password'      => 'app_p2p_mange',
        'hostport'      => '27017',
        'pk_convert_id' => true,
    ]
];