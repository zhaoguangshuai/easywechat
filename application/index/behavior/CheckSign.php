<?php
namespace app\index\behavior;

use think\Request;
use app\index\helper\RedisHelper;

class CheckSign
{
    //前置行为验证用户是否登陆
    public function run(Request $request)
    {

        $signKey = config('login.signkey');
        $postData = $request->param();
        $api_sign = $postData['api_sign'];
        //将签名从参数中删除掉
        unset($postData['api_sign']);
        //按照键名对传过来的参数进行升序排序
        ksort($postData);
        $info = $signKey;
        foreach ($postData as $k => $v) {
            if (!is_array($v)) {
                $info .= $k . $v;
            }
        }
        //得到加密的字符串,前面有加密盐后面也有加密盐
        $info .= $signKey;
        /*var_dump($info);
        var_dump(strtoupper(md5(md5($info))));exit;*/
        //对加密字符串进行两个md5加密，然后转换成大写。
        //与传过来的签名进行对比,如果不同说明传过来的数据有可能被串改，或者是异常的请求
        if (strtoupper(md5(md5($info))) !== $api_sign) {
            exit(json_encode(['code' => 201, 'msg' => '签名错误!', 'data' => []]));
        }

    }
}