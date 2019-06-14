<?php
namespace app\index\controller;

use think\Controller;
use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;
use app\index\helper\RedisHelper;
use Carbon\Carbon;

class Zhifubao extends Controller
{
    protected $config = [
        'app_id' => '2016092700607247',
        'notify_url' => 'http://easywechat.szbchm.com/index.php/index/Zhifubao/notify.html',
        'return_url' => 'http://easywechat.szbchm.com/index.php/index/Zhifubao/showPayType.html',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAsOa86VBWwKjEL2j+6axD+SRpWDmhuPpNLg62sytyjZ/FHMaVi6Aw6tVE9I43GGM1IxB86HbvhU8oVArhkt19yYFTG4VQtOg+UvkISGwm3NBFrHHPUVlnZwIb4iF0XVI0V+IFg0NwJHGyqAjGeeQXG4OS3b07SByJrJwXgOpmXl0UjnF1nKjnj6utoz1qOhd/ldZ2AE9JWhtsdzt45q9mq7tpiIXaRsBgBVZOU86SGJBdOISHZZfXHMk0SSzf+0scpYVCpO5l9+R86GbfPuUUaa1pT9/V2t8FL/cSf+GXgKDBXiC0gHduq5uzOyZxNe5+Y6ee0ZdPC5XjCEG+ILcTTQIDAQAB',
        // 加密方式： **RSA2**
        'private_key' => 'MIIEowIBAAKCAQEA0KCFOwMd6l0r7d46yxRaeWVXNDwvskDZ3ZmaWLfkU1Cc8pLLE4xh3zDUHT74k3Yo5TG/8VJOEYrXAavalqBkfqJSMRIrM5CXprH1oW+UONMcTeeftGiwCZvebn0wOQBJhYpG63X/Hs7QklQUQOf/Qe3Uft7lerVtEItZPAxjw9eTdDRDXS8tji82ckR5bWen3IFuHCh00gAJ05AiFd/nmPxBbarQ/AJNXbkK3q0hYgLZYVxih+G6UHNlJHP5KPjjdG6ULMWmIdE68LKYgpRdVpbfId9zG0w3CC2jxR65yFVsb9inWyWSYjOdkjmeMHffvACr/b4vCRksuX5CQtNA+QIDAQABAoIBAHtklahjnYwCc3sLF6cJcMGgSMWiW2uIo9NHiFy01xzX7xwh6m6zdye1D6AEoK1HE8ULecVXssj5X1WjuXOhhg7IHujbICYtfSSg2PEUt4wsgO8q8fCYgpYMVLDw5lawbsvHD08wySYFmKMTi68gCH+MFUS8vOmo6MsCQFMd2Nv5UY57GkJzkA6VOTx6oY+7ZJbURb2fwRxMgBBnTtf0rOq0Wq/X3ySekZAgV5zJnFZJ10HeiUEIlm5Hsnsu3EgZ7aHoFZMYiOVpYClj4B8eg+K4dUe7PWTRSFZzX/skFFssUGn5SRe0p7XK2R20etDbSoFR6gBj3wVbCuY8wlYA5+kCgYEA8rtjXrPRnDNLeyDH9h7IPaVmuW5LEkuAtUdKKHLie4t78fpGqwIFk0E8u2d1vM1L1zUH8eeKw7sVkOEaW0U1+MowMzOCqo+y1L+LcTcx/fLsff3qfLaB2OMdnG3j3RdIznC3uLEbnlLfT4xAOBcnIBOGj0AUTn8gvvkEPfxWJ5MCgYEA3AfkjmXUe76UzArpnhDWuzXRP3A8XVV+yUtveBAmlpN044UuBnvEBTZFEqh2xkmEFAvuE9L0+HdynWNLFzFthgByPXKH8iqc2ZejzuvC74QGXZln/ZeWTd+PSvCmXzZ68/9NZjlNpwKKHIoayYoJ6f0CuOgate6zTilATbl59MMCgYBccBxxrk0/DRHLvDoqcqKTR3ANYgFY4EQNxS5qpQW3QvFav5M2ALka0RdNzyK5Wf1t4ZY6+5CO5apa2D7vTzY0ntsoRI+YYM0b1C5IWVrCeUay3IX2JJLig+t87SR/yCRD6g0tWtVHwAunAaRbOAYAYy81UpPpKOTRlYeWC4jL3QKBgQC8aUnOoeYEnJqf6oxH3RVzM89dakdWiNpLlnv6LKJOeDA0j5ts09hzuhgFtRoYC2W9IMYaVZ+NkAMmQJtRXfLyNzmb4pIEzXaIYPBVRM8k+EHXLoC8x2xefJwgSNe+WlGI1Eo7yb/zzhnj/GKZx8lbg1ssaRxOQ/toZ6x3oeaLrwKBgAvPNM2fuv+BCLYUsyt5CObc1SPRGq3wWqfvt4gYKblVj7mzNp/F3FR4xFvgHpHyiLCSNVnXNxS+LFWHU/9vkU5tQWSwlCWvPE9DvHxsN8nu//6ip1BakI0xH77t6wILEAe37j27l4Z5uAsRRtxoTNPiosxqrLWFlcyTg9sKT3bk',
        'log' => [ // optional
            'file' => './logs/alipay.log',
            'level' => 'debug', // 建议生产环境等级调整为 info，开发环境为 debug
            'type' => 'single', // optional, 可选 daily.
            'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
        ],
        'http' => [ // optional
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'dev', // optional,设置此参数，将进入沙箱模式
    ];

    public function showPayType()
    {
        return $this->fetch();
    }

    public function index()
    {
        $type = \request()->get('type');
        $time = time();
        $order = [
            'out_trade_no' => $time,
            'total_amount' => '0.01',
            'subject' => 'test subject - 测试',
        ];
        //保存订单信息
        RedisHelper::getInstance()->hMSet('orderinfo:'.$time, array_merge($order, ['status' => 1]));
        trace('订单信息'.json_encode(array_merge($order, ['status' => 1])));
        if($type == 1){
            $alipay = Pay::alipay($this->config)->web($order);
        }elseif ($type == 2){
            $alipay = Pay::alipay($this->config)->wap($order);
        }elseif ($type == 3){
            $alipay = Pay::alipay($this->config)->pos($order);
        }else{
            $alipay = Pay::alipay($this->config)->scan($order);
        }


        return $alipay->send();// laravel 框架中请直接 `return $alipay`
    }

    /*public function return()
    {
        $data = Pay::alipay($this->config)->verify(); // 是的，验签就这么简单！

        // 订单号：$data->out_trade_no
        // 支付宝交易号：$data->trade_no
        // 订单总金额：$data->total_amount
    }*/

    public function notify()
    {
        $alipay = Pay::alipay($this->config);

        try{
            $data = $alipay->verify(); // 是的，验签就这么简单！
            trace('支付宝回调信息'.json_encode($data));
            $time = $data->out_trade_no;
            //修改订单状态
            $res = RedisHelper::getInstance()->hSet('orderinfo:'.$time, 'status', 2);
            !empty($res) ? trace($time.'success!') : trace($time.'errir!');
            // 请自行对 trade_status 进行判断及其它逻辑进行判断，在支付宝的业务通知中，只有交易通知状态为 TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功。
            // 1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号；
            // 2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额）；
            // 3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）；
            // 4、验证app_id是否为该商户本身。
            // 5、其它业务逻辑情况

            Log::debug('Alipay notify', $data->all());
        } catch (\Exception $e) {
            trace('回调错误信息############'.$e->getMessage());
        }

        return $alipay->success()->send();// laravel 框架中请直接 `return $alipay->success()`
    }

}
