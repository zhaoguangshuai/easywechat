<?php
/**
 * Created by PhpStorm.
 * User: zgs
 * Date: 2019/6/28
 * Time: 10:37
 */
namespace app\common;

class ValidEmail
{
    //测试绑定类库到容器 验证邮箱
    public function valiEmail($email)
    {
        $check = 0;
        if(filter_var($email,FILTER_VALIDATE_EMAIL))
        {
            $check = 1;
        }
        return $check;
    }
}