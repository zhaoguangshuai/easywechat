<?php
namespace app\index\controller;

use think\Controller;
use app\index\helper\RedisHelper;

//服务器负载均衡共享session
class Sharesession extends Controller
{
    public function sessionRedis()
    {
        //session_start();
        //销毁session
        /*var_dump('PHPREDIS_SESSION:bo0hjmse69df3r33eddk75kci2');
        echo "<br/>";
        var_dump('PHPREDIS_SESSION:' . session_id());exit;*/
        /*$res = RedisHelper::getInstance()->del('PHPREDIS_SESSION:' . session_id()) . '<br/>';
        //$res = session_destroy();
        var_dump($res);
        exit;*/
        //更改session的存储方式
        ini_set("session.save_handler", "redis");
        ini_set("session.save_path", "tcp://127.0.0.1:6379");

        session('userinfo', ['name' => 'zgs12', 'userid' => 12200112]);

        //检查session_id
        echo 'session_id:' . session_id() . '<br/>';

        //redis存入的session（redis用session_id作为key,以string的形式存储）
        echo "<br/>";
        $userinfo =  RedisHelper::getInstance()->get('PHPREDIS_SESSION:' . session_id()) . '<br/>';
        var_dump($userinfo);
        var_dump(session('userinfo'));
        exit;

        return $this->fetch();
    }

}
