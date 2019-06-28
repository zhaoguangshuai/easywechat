<?php
/**
 * Created by PhpStorm.
 * User: zgs
 * Date: 2019/6/28
 * Time: 10:37
 */
namespace app\common;

class GetIp
{
    //测试绑定类库到容器 获取正式IP
    public function getRealIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
        {
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            //to check ip is pass from proxy
        {
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            $ip=$_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}