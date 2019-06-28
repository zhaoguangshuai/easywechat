<?php
namespace app\index\controller;

use think\Controller;
use \Firebase\JWT\JWT;
use think\Request;
use app\index\helper\RedisHelper;

class Login extends Controller
{
    //jwt登陆
    public function demoLogin(Request $request)
    {
        $username = $request->post('username', '', 'trim');
        $password = $request->post('password', '', 'trim');
        if(md5($password) != 'e10adc3949ba59abbe56e057f20f883e') return json(['code' => 100, 'msg' => '密码不对', 'data' => []]);

        $userid = 10010;
        $expirationTime = config('login.expiration_time');
        $currentTime = time();
        $expTime = $currentTime + $expirationTime;
        $token = array(
            "userid" => $userid,
            "iat" => $currentTime,  //生成时间
            //"exp" => $expTime  //过期时间，不设置过期时间则永远不过期
        );

        //$key = config('login.key');
        //$jwt = JWT::encode($token, $key); //对称性加密
        $privateKey = config('login.privatekey');
        $jwt = JWT::encode($token, $privateKey, 'RS256');  //非对称性加密
        //将token存到redis并设置有效期
        RedisHelper::getInstance()->setex('token:apiauth'.$userid, $expirationTime, $jwt);

        return json(['code' => 200, 'msg' => '登陆成功', 'data' => ['userid' => $userid, 'token' => $jwt]]);
        /*var_dump($jwt);
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        print_r($decoded);
        print_r($this->object_array($decoded));
        exit;

        return $this->fetch();*/
    }

    //调用这个函数，将其幻化为数组，然后取出对应值
    public function object_array($array)
    {
        if(is_object($array))
        {
            $array = (array)$array;
        }
        if(is_array($array))
        {
            foreach($array as $key=>$value)
            {
                $array[$key] = self::object_array($value);
            }
        }
        return $array;
    }

}
