<?php
/**
 * Created by PhpStorm.
 * User: zgs
 * Date: 2019/6/27
 * Time: 9:40
 */
//注册路由中间件，指定的路由会用到
return [
    'inappcheck' => app\http\middleware\InAppCheck::class,
    'auth' => app\http\middleware\Auth::class,
];