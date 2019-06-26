<?php
namespace app\index\controller;

use think\Controller;
use \Firebase\JWT\JWT;

class Login extends Controller
{
    //jwt登陆
    public function demoLogin()
    {
        $key = "example_key";
        $token = array(
            "iss" => "http://example.org",
            "aud" => "http://example.com",
            "iat" => 1356999524,
            "nbf" => 1357000000
        );

        /**
         * IMPORTANT:
         * You must specify supported algorithms for your application. See
         * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
         * for a list of spec-compliant algorithms.
         */
        $jwt = JWT::encode($token, $key);
        var_dump($jwt);
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        print_r($decoded);
        print_r($this->object_array($decoded));
        exit;

        return $this->fetch();
    }

    //调用这个函数，将其幻化为数组，然后取出对应值
    public function object_array($array)
    {
        if(is_object($array))
        {
            $array = (array)$array;
        }
        if(is_array($array))
        {
            foreach($array as $key=>$value)
            {
                $array[$key] = self::object_array($value);
            }
        }
        return $array;
    }

}
