<?php
namespace app\index\controller;

use think\Controller;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Image;
use app\index\helper\RedisHelper;
use EasyWeChat\Factory;

class Index extends Controller
{
    public $app;
    public function index()
    {
        //    先初始化微信
        $input = file_get_contents('php://input');
        trace('微信数据',$input);
        $obj = simplexml_load_string($input, 'SimpleXMLElement', LIBXML_NOCDATA);
        trace('微信json数据',json_encode($obj));
        $message_text = json_decode(json_encode($obj), true);
        $this->app = app('wechat.official_account');
        $this->app->server->push(function ($message) {
            trace('message数据',json_encode($message));
            switch ($message['MsgType']) {
                case 'event':
                        switch ($message['Event']) {
                            case 'subscribe':  //订阅公众号
                                //$resinfo = $this->sendMessage($message); //推送带参数的二维码图文消息
                                return '欢迎关注帅帅demo公众号!本公众号正在做一元购活动,点击 "一元购" 菜单栏即可获取推广二维码,分享推广二维码邀请十位好友关注本公众号即可一元购买IPhone XS。发送“一元购”消息即可获取当前有几位好友给你助力。';
                                break;
                            case 'unsubscribe': //取消订阅公众号
                                return '取消订阅公众号';
                                break;
                            case 'SCAN':  //扫描带参数二维码事件,用户已经关注时，进行关注后的事件推送
                                return '扫描带参数二维码事件,用户已经关注时，进行关注后的事件推送';
                                break;
                            case 'LOCATION':  //上报地理位置事件
                                return '上报地理位置事件';
                                break;
                            case 'CLICK':  //自定义菜单事件  点击菜单拉取消息时的事件推送
                                if($message['EventKey'] == 'V1001_TODAY_MUSIC'){  //一元购点击事件
                                    return new Image(RedisHelper::getInstance()->get('source:mediaid:'.$message['FromUserName']));
                                }elseif ($message['EventKey'] == 'V1001_GOOD'){ //赞一下我们点击事件
                                    //return '赞一下我们点击事件';
                                    $image_url = 'http://easywechat.szbchm.com/static/wechat_img/20190612/hechengo_9S61YjnD7VstIaFelLF8QPAOew.jpg';
                                    $items = [
                                        new NewsItem([
                                            'title'       => '一元购',
                                            'description' => '分享二维码,邀请十位好友关注公众号就可以一元购买挂历。',
                                            'url'         => 'www.baidu.com',
                                            'image'       => $image_url,
                                        ]),
                                    ];
                                    return new News($items);
                                }else{
                                    return '未知点击事件';
                                }
                                break;
                            case 'VIEW':   //自定义菜单事件  点击菜单跳转链接时的事件推送
                                return '自定义菜单事件  点击菜单跳转链接时的事件推送';
                                break;
                            default:
                                return '收到其它消息';
                                break;
                        }
                    break;
                case 'text':
                    //return '收到文字消息';
                    if($message['Content'] == '一元购'){
                        $count = RedisHelper::getInstance()->get('subscribe:count:'.$message['FromUserName']);
                        $count == '' ? $count = 0 : $count = $count;
                        $textcontent = '已经有'.$count.'人通过您分享的二维码关注公众号!';
                        return new Text($textcontent);
                    }elseif ($message['Content'] == '我是谁'){
                        //用户的信息
                        $userinfo = $this->app->user->get($message['FromUserName']);
                        return new Text('My name is '.$userinfo['nickname']);
                    }else{
                        return new Text('您好！帅那个帅。');
                    }
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
        });
        $this->app->server->serve()->send();
        //判断当前用户是否已经关注过公众号，没有关注过就生成推广二维码，关注过就不用生成了
        $ismemres = RedisHelper::getInstance()->sIsMember('follow:aggregate',$message_text['FromUserName']);
        if (empty($ismemres)){
            if($message_text['Event'] == 'subscribe' && !empty($message_text['EventKey'])){ //扫描带参数二维码事件,用户未关注时，进行关注后的事件推送
                $this->sendHuodongXiao($message_text); //给分享者推广人数加1，并推送模板消息
                $this->sendMessage($message_text); //生成推广二维码
            }elseif ($message_text['Event'] == 'subscribe'){  //手动搜索关注公众号
                $this->sendMessage($message_text);  //生成推广二维码
            }
        }
    }

    //推送一元购活动消息，给分享的人发送已经有几个人关注了，现在是那个好友关注了
    protected function sendHuodongXiao($message)
    {
        //获取分享用户的openid
        $fxopenid = ltrim($message['EventKey'], 'qrscene_');
        trace('分享用户openid',$fxopenid);
        //给分享用户的分享关注数量加1
        $count = RedisHelper::getInstance()->incr('subscribe:count:'.$fxopenid);
        trace('关注数量加1redis返回信息',$count);
        //当前关注用户的信息
        $userinfo = $this->app->user->get($message['FromUserName']);
        trace('当前关注用户的信息',json_encode($userinfo));
        //给分享者推送消息
        $textcontent = '好友'.$userinfo['nickname'].'已关注，已有'.$count.'人助力您!';
        trace('给分享者推送消息内容',$textcontent);
        //$this->app->broadcasting->sendText($textcontent, [$fxopenid, $message['FromUserName']]);
        //if($count == 2){
        $this->app->template_message->send([
                    'touser' => $fxopenid,
                    'template_id' => 'SY_ifMultrJYu6QjNSzC0hWtfH28Oeeh3-rEU7nPauQ',
                    'url' => 'https://www.baidu.com/',
                    'data' => [
                        'first' => '至尊乐欢迎您！',
                        'keynote1' => ['一元购活动', '#F00'], // 指定为红色
                        'keynote2' => ['value' => $textcontent, 'color' => '#F00'], // 与第二种一样
                        'remark' => '至尊乐欢迎您',
                    ],
                ]);
        //}
        /*$resmessage = new Raw("<xml><ToUserName><![CDATA[{$fxopenid}]]></ToUserName><FromUserName><![CDATA[{$fromUser}]]></FromUserName><CreateTime>12345678</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[{$textcontent}]]></Content></xml>");
        trace('推送结果返回',json_encode($resmessage));*/
    }

    //设置菜单栏
    /*protected function returnEvent($message)
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

    }*/

    //推送带参数的二维码图文消息
    public function sendMessage($message)
    {
        //将关注的用户加入有续集
        RedisHelper::getInstance()->sAdd('follow:aggregate',$message['FromUserName']);
        $result = $this->app->qrcode->temporary($message['FromUserName'], 6 * 24 * 3600);
        trace('获取带参数二维码',json_encode($result));
        $url = $this->app->qrcode->url($result['ticket']);

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
        $user = $this->app->user->get($message['FromUserName']);
        trace('用户信息',json_encode($user));
        $headimgcontent = file_get_contents($user['headimgurl']); // 得到二进制图片内容
        $headfilename = $path.'/headimg'.$message['FromUserName'].'.jpg';
        //将该用户微信图片保存到服务器
        $res2 = file_put_contents($headfilename, $headimgcontent); // 写入文件
        trace('文件写入返回值2',$res2);

        $image = \think\Image::open($filename);
        // 添加水印图片
        $hechengname = $path.'/hecheng'.$message['FromUserName'].'.jpg';
        $image->water($headfilename,\think\Image::WATER_SOUTHEAST)->text($user['nickname'],'simkai.ttf',20,'#FF3030',\think\Image::WATER_SOUTHWEST)->save($hechengname);
        trace('测试日志',1111111111111111);
        //分享图片链接地址
        $image_url = 'http://easywechat.szbchm.com'.trim($hechengname,'.');
        trace('图片链接地址',$image_url);
        //$mediaIdres = $app->media->uploadImage($image_url);
        $result = $this->app->material->uploadImage($hechengname);
        //$result = $app->media->uploadImage($hechengname);
        trace('上传素材返回信息',json_encode($result));
        //return new Image('6Y0ORPyd40WcARxy5vkmFzr49mVh8eIiqilneLrOX9w');
        $res = RedisHelper::getInstance()->set('source:mediaid:'.$message['FromUserName'], $result['media_id']);
        trace('redis返回信息',$res);
            //return $result['media_id'];

            /*
             *  title 标题
                description 描述
                image 图片链接
                url 链接 URL
            */
            /*$image_url = 'http://easywechat.szbchm.com'.trim($hechengname,'.');
            trace('分享二维码访问地址',$image_url);
            $items = [
                new NewsItem([
                    'title'       => '一元购',
                    'description' => '分享二维码,邀请十位好友关注公众号就可以一元购买挂历。',
                    'url'         => 'www.beidu.com',
                    'image'       => $image_url,
                ]),
            ];
            return new News($items);*/


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
                        "name" => "网页授权",
                        "url"  => "http://easywechat.szbchm.com/index.php/Index/index/testWebOauth"
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

    //设置菜单栏
    public function test()
    {
        var_dump(RedisHelper::getInstance());exit;
        $app = app('wechat.official_account');
        $result = $app->material->uploadImage('.\static\wechat_img\20190610\qwer.jpg');
        //$res = $app->media->uploadImage('.\static\wechat_img\20190610\qwer.jpg');
        var_dump($result);

        exit;
        //添加水印文字
        $images = \think\Image::open('.\static\wechat_img\20190610\qwer.jpg');
        // 给原图左上角添加水印并保存water_image.png
        $zhongjiname = '.\static\wechat_img\20190610\zhongji.jpg';
        $images->text('帅帅','simkai.ttf',20,'#FF3030',\think\Image::WATER_SOUTHEAST)->save($zhongjiname);
    }

    //测试网页授权
    public function testWebOauth()
    {
        $config = [
            'debug'   => true,
            'app_id'  => 'wxbf34ee861a89cc1c',
            'secret'  => 'a6b8adc5dd702f6e21b386e10d0df1c9',
            /*'token'   => DynamicConfig::WECHAT_TOKEN,
//            'aes_key' => 'KonTCWjsdo4UGiLGCEnmIMClRZzfegzJx3kOqGSOfX0', // 可选
            'log'     => [
                'level' => 'debug',
                'file'  => APP_ROOT_PATH . '/../logs/Home_easywechat.log', // XXX: 绝对路径！！！！
            ],*/
            'oauth' => [
                'scopes'   => ['snsapi_userinfo'],
                'callback' => 'http://easywechat.szbchm.com/index.php/Index/index/oauth_callback',
            ],
        ];
        $app = Factory::officialAccount($config);
        $oauth = $app->oauth;
        //return $oauth->redirect();
        // 这里不一定是return，如果你的框架action不是返回内容的话你就得使用
        $oauth->redirect()->send();

    }

    //授权后重定向的回调链接地址
    public function oauth_callback()
    {
        $config = [
            'debug'   => true,
            'app_id'  => 'wxbf34ee861a89cc1c',
            'secret'  => 'a6b8adc5dd702f6e21b386e10d0df1c9',
            /*'token'   => DynamicConfig::WECHAT_TOKEN,
//            'aes_key' => 'KonTCWjsdo4UGiLGCEnmIMClRZzfegzJx3kOqGSOfX0', // 可选
            'log'     => [
                'level' => 'debug',
                'file'  => APP_ROOT_PATH . '/../logs/Home_easywechat.log', // XXX: 绝对路径！！！！
            ],*/
            'oauth' => [
                'scopes'   => ['snsapi_userinfo'],
                'callback' => 'http://easywechat.szbchm.com/index.php/Index/index/oauth_callback',
            ],
        ];

        $app = Factory::officialAccount($config);
        $oauth = $app->oauth;

        // 获取 OAuth 授权结果用户信息
        $user = $oauth->user();

        $wechat_user = $user->toArray();
        trace('授权重定向用户信息',json_encode($wechat_user));
        //将用户信息保存到redis
        //if(!empty($wechat_user))

        $targetUrl = 'http://easywechat.szbchm.com/index.php/Index/index/showWebPage';
        header('location:'. $targetUrl); // 跳转到 user/profile
    }

    //最后所展示的网页
    public function showWebPage()
    {
        return $this->fetch();
    }
}
