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
        $app->server->push(function ($message) {
            trace('message数据',json_encode($message));
            switch ($message['MsgType']) {
                case 'event':
                    return $this->returnEvent($message);
                    break;
                case 'text':
                    return '收到文字消息';
                    break;
                case 'image':
                    return '收到图片消息';
                    break;
                case 'voice':
                    return '收到语音消息';
                    break;
                case 'video':
                    return '收到视频消息';
                    break;
                case 'location':
                    return '收到坐标消息';
                    break;
                case 'link':
                    return '收到链接消息';
                    break;
                case 'file':
                    return '收到文件消息';
                // ... 其它消息
                default:
                    return '收到其它消息';
                    break;
            }

            // ...
        });
        $app->server->serve()->send();

    }

    //设置菜单栏
    protected function returnEvent($message)
    {
        switch ($message['Event']) {
            case 'subscribe':  //订阅公众号
                //return $this->returnEvent($message);
                return '订阅公众号';
                break;
            case 'unsubscribe': //取消订阅公众号
                return '取消订阅公众号';
                break;
            case 'subscribe':  //扫描带参数二维码事件,用户未关注时，进行关注后的事件推送
                return '扫描带参数二维码事件,用户未关注时，进行关注后的事件推送';
                break;
            case 'SCAN':  //扫描带参数二维码事件,用户已经关注时，进行关注后的事件推送
                return '扫描带参数二维码事件,用户已经关注时，进行关注后的事件推送';
                break;
            case 'LOCATION':  //上报地理位置事件
                return '上报地理位置事件';
                break;
            case 'CLICK':  //自定义菜单事件  点击菜单拉取消息时的事件推送
                //return '自定义菜单事件  点击菜单拉取消息时的事件推送';
                return $this->sendMessage($message); //推送带参数的二维码图文消息
                break;
            case 'VIEW':   //自定义菜单事件  点击菜单跳转链接时的事件推送
                return '自定义菜单事件  点击菜单跳转链接时的事件推送';
                break;
            default:
                return '收到其它消息';
                break;
        }

    }

    //推送带参数的二维码图文消息
    public function sendMessage($message)
    {
        if($message['EventKey'] == 'V1001_TODAY_MUSIC'){  //一元购点击事件
            $app = app('wechat.official_account');
            $result = $app->qrcode->temporary($message['FromUserName'], 6 * 24 * 3600);
            trace('获取带参数二维码',json_encode($result));
            $url = $app->qrcode->url($result['ticket']);

            $content = file_get_contents($url); // 得到二进制图片内容

            $path = './static/wechat_img/'.date('Ymd');
            trace('图片保存路径',$path);
            if(!file_exists($path))
            {
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                mkdir($path, 0777);
            }

            $filename = $path.'/qr'.$message['FromUserName'].'.jpg';
            trace('文件保存路径',$filename);
            //将带参数的二维码保存到服务器
            $res = file_put_contents($filename, $content); // 写入文件
            trace('文件写入返回值1',$res);

            //将微信用户图片保存到本地
            //获取用户信息
            $user = $app->user->get($message['FromUserName']);
            trace('用户信息',json_encode($user));
            $headimgcontent = file_get_contents($user['headimgurl']); // 得到二进制图片内容
            $headfilename = $path.'/headimg'.$message['FromUserName'].'.jpg';
            //将该用户微信图片保存到服务器
            $res2 = file_put_contents($headfilename, $headimgcontent); // 写入文件
            trace('文件写入返回值2',$res2);

            $image = \think\Image::open($filename);
            // 给原图左上角添加水印并保存water_image.png
            $image->water($headfilename,\think\Image::WATER_SOUTHEAST)->save($path.'/hecheng'.$message['FromUserName'].'.jpg');

            // Array
            // (
            //     [ticket] => gQFD8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyTmFjVTRWU3ViUE8xR1N4ajFwMWsAAgS2uItZAwQA6QcA
            //     [expire_seconds] => 518400
            //     [url] => http://weixin.qq.com/q/02NacU4VSubPO1GSxj1p1k
            // )
        }elseif ($message['EventKey'] == 'V1001_GOOD'){ //赞一下我们点击事件

        }else{
            return '未知点击事件';
        }

    }

    //设置菜单栏
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
