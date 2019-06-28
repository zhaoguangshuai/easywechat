<?php
namespace app\index\behavior;

use \Firebase\JWT\JWT;
use think\Request;
use app\index\helper\RedisHelper;

class UserCheck
{
    //前置行为验证用户是否登陆
    public function run(Request $request)
    {
        /*if ('user/0' == request()->url()) {
            return false;
        }*/
        //var_dump(request()->url());exit;
        try{
            $apiauth = $request->header('apiauth');
            $userid = $request->param('userid');
            //解密秘钥
            //$key = config('login.key');
            //$userinfo = JWT::decode($apiauth, $key, array('HS256'));
            $publicKey = config('login.publickey');
            $userinfo = JWT::decode($apiauth, $publicKey, array('RS256'));
            //var_dump($userinfo);exit;
            //验证解密出来的用户ID与传过来的是否匹配
            if ($userinfo->userid != $userid) exit(json_encode(['code' => 101, 'msg' => '用户ID与秘钥不匹配!', 'data' => []]));

            //验证token是否过期
            $token = RedisHelper::getInstance()->get('token:apiauth'.$userid);
            if (empty($token)) exit(json_encode(['code' => 102, 'msg' => 'token已经过期,请重新登陆!', 'data' => []]));

        } catch (\Exception $e) {
            //var_dump($e->getMessage());exit;
            exit(json_encode(['code' => 100, 'msg' => '秘钥不对,非法请求!', 'data' => []]));
        }
        /*$key = config('login.key');
        $apiauth = $request->header('apiauth');
        //解密秘钥
        $decoded = JWT::decode($apiauth, $key, array('HS256'));
        var_dump($apiauth);exit;
        echo json_encode(['code' => 100, 'msg' => '没有该权限', 'data' => []]);exit;*/
    }
}