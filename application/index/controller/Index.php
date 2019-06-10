<?php
namespace app\index\controller;

use think\Controller;

class Index
{
    public function index()
    {
    	/*$echoStr = $_GET["echostr"];
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        		
		$token = 'n1G5F101uhuMXw9sZD8dGgNS19YFsOg';
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			echo $echoStr;
        	exit;
		}else{
			echo '验证失败';
		}*/
        //    先初始化微信
        $input = file_get_contents('php://input');
        trace('微信数据',$input);
        $obj = simplexml_load_string($input, 'SimpleXMLElement', LIBXML_NOCDATA);
        trace('微信json数据',json_encode($obj));
        $app = app('wechat.official_account');
        $app->server->push(function($message){
            return 'hello,world';
        });
        $app->server->serve()->send();

    }

    public function hello()
    {
        $app = app('wechat.official_account');
        $buttons = [
            [
                "type" => "click",
                "name" => "一元购",
                "key"  => "V1001_TODAY_MUSIC"
            ],
            [
                "name"       => "菜单",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "搜索",
                        "url"  => "http://www.baidu.com/"
                    ],
                    [
                        "type" => "view",
                        "name" => "视频",
                        "url"  => "http://v.qq.com/"
                    ],
                    [
                        "type" => "click",
                        "name" => "赞一下我们",
                        "key" => "V1001_GOOD"
                    ],
                ],
            ],
        ];
        $app->menu->create($buttons);
    }
}
