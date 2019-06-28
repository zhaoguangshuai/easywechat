<?php
namespace app\index\controller;

use think\Controller;
use JPush\Client as JPush;

class JiGuang extends Controller
{
    //极光推送
    public function index()
    {
        $app_key = 'd9a2e9c2fb43558cd1abc97d';
        $master_secret = '33021ec3745130912a256367';
        $client = new JPush($app_key, $master_secret);
        //var_dump($client);exit;
        $res = $client->push()
            ->setPlatform('all')
            ->addAllAudience()
            ->setNotificationAlert('Hello, JPush')
            ->send();
        var_dump($res);

    }

}