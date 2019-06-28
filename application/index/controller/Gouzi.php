<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;

class Gouzi extends Controller
{
    //测试钩子与行为
    public function testIndex()
    {
        var_dump($this->request->Auth);
        var_dump($this->request->InApp);exit;
        return json(['code' => 200, 'msg' => '测试成功', 'data' => []]);
    }

    //测试中间件
    public function testMidd(Request $request)
    {
        //var_dump($request);
        var_dump($this->request->InApp);exit;
        return json(['code' => 200, 'msg' => '测试成功', 'data' => []]);
    }

    //测试前置行为验证是否登陆
    public function testBefore()
    {
        return json(['code' => 200, 'msg' => '测试JWT成功', 'data' => []]);
    }

    //测试前置行为验证签名
    public function verifySign(Request $request, $type, $userid)
    {
        dump($request);
        dump($userid);
        echo $type;exit;
        return json(['code' => 200, 'msg' => '签名验证成功', 'data' => []]);
    }


}
