<?php
namespace app\index\controller;

use think\Controller;
use JPush\Client as JPush;
use think\Request;

class JiGuang extends Controller
{
    //极光推送//
    public function index()
    {
        $app_key = '9503595df05a404bcc7d03d9';
        $master_secret = 'a548ad4487efc2b252e195a6';
        $client = new JPush($app_key, $master_secret);
        //var_dump($client);exit;
        $res = $client->push()
            ->setPlatform('all')
            ->addAllAudience()
            ->setNotificationAlert('Hello, JPush')
            ->send();
        var_dump($res);

    }

    //iscli
    public function getCli()
    {
        var_dump(request()->isCgi());
        echo '<br/>';
        var_dump(request()->ext());
        echo '<br/>';
        var_dump(request()->token());
    }

    //下载文件
    public function download()
    {
        $download =  new \think\response\Download('image.jpg');
        return $download->name('my.jpg');
        // 或者使用助手函数完成相同的功能
        // download是系统封装的一个助手函数
        //return download('image.jpg', 'my.jpg');
    }

}