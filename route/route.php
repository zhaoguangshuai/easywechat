<?php
use think\Request;
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});

Route::get('hello/:id', 'Index/index/hello');

Route::get('testIndex', 'Index/Gouzi/testIndex')->middleware(['Auth']);

//Route::post('testMidd', 'Index/Gouzi/testMidd')->ext('html')->https(false)->domain('127.0.0.1');

//中间件传入参数admin
Route::post('testMidd', 'Index/Gouzi/testMidd')
    ->middleware(['InAppCheck:admin']);

//闭包支持 依赖注入
Route::rule('testContain', function (Request $request, $name) {
    $method = $request->method();
    return '[' . $method . '] Hello,' . $name;
});

//测试前置行为验证登陆
Route::rule('testBefore', 'Index/Gouzi/testBefore')
    ->before(['\app\index\behavior\UserCheck']);

//测试前置行为验证签名
Route::rule('verifySign', 'Index/Gouzi/verifySign')
    ->before(['\app\index\behavior\CheckSign']);

//测试容器
Route::rule('rongqi', 'Index/RongQi/index');

//测试容器
Route::rule('validRongQi', 'Index/RongQi/validRongQi');
/*return [
    //公共访问接口
    '[mom]' => [
        //'testMidd' => ['Index/Gouzi/testMidd', ['method' => 'post|get', 'https' => true]],
        'testMidd' => ['Index/Gouzi/testMidd', ['method' => 'post|get', 'ext' => 'html']],
    ],
];*/
