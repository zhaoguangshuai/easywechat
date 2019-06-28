<?php

namespace app\http\middleware;

class Auth
{
    public function handle($request, \Closure $next)
    {
        //$request->Auth = '123';
        return redirect('index/Carbondemo/index');
        return $next($request);
    }
}
