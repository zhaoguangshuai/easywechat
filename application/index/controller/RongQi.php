<?php
namespace app\index\controller;

use think\Controller;
use app\common\GetIp;

class RongQi extends Controller
{
    //绑定类到容器，使用app获取类对象
    public function index()
    {
        $res = app('validemail')->valiEmail('blog@koonk.com');
        echo $res;
        echo '<br/>';
        $ip = app('getip')->getRealIpAddr();
        var_dump($ip);
        //$userid = app('request')->post('userid');
        //dump($userid);exit;

    }

    //使用依赖注入获取对象
    public function validRongQi(GetIp $getIp, $type, $userid)
    {
        var_dump($getIp->getRealIpAddr());
        var_dump($type);
        var_dump($userid);

    }
}