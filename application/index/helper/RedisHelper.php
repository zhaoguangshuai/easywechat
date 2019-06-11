<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/1
 * Time: 20:05
 */
namespace app\index\helper;



class RedisHelper {
    private static $_instance = null;

    public static function getInstance() {
        if (self::$_instance === null) {
            $host = config('redis.host');
            $port = config('redis.port');
            $password = config('redis.password');
            self::$_instance = new \Redis;
            self::$_instance->connect($host, $port);
            if ($password != '') {
                self::$_instance->auth($password);
            }
        }
        return self::$_instance;
    }


}