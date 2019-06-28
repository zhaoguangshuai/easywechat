<?php

namespace app\http\middleware;

class InAppCheck
{
    //中间件传入参数$name
    public function handle($request, \Closure $next, $name)
    {
        if (preg_match('~micromessenger~i', $request->header('user-agent'))) {
            $request->InApp = 'WeChat';
        } else if (preg_match('~alipay~i', $request->header('user-agent'))) {
            $request->InApp = 'Alipay';
        } else {
            //$request->InApp = \request()->param('name');
            $request->InApp = $name;
        }
        return $next($request);
    }
}
