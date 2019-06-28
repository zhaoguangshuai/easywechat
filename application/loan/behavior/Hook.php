<?php
/**
 * Created by PhpStorm.
 * User: long
 * Date: 2018/8/17
 * Time: 16:05
 */
namespace app\loan\behavior;
use think\Db;
use app\index\helper\RedisHelper;
use Carbon\Carbon;

class Hook
{
    /**
     * 测试钩子和行为
     * @param $data
     */
    public function run($data)
    {
        $data         = is_object($data) ? $data : (object)$data;
        $now_time     = time();
        //$add_date = date('Y-m-d His', $now_time);
        $add_date = Carbon::now()->format('Y-m-d H:i:s');
        //$mongo_config = config('auth_' . check_env() . '.MONGO');
        $log_data     = [
            'method'      => request()->controller() . '/' . request()->action(),
            //'user_id'     => request()->post('user_id','0'),
            'header_data' => (request()->header()),
            'param_data'  => (request()->param()),
            'return_data' => ($data->getData()),
            'add_date'    => $add_date,
            'add_time'    => $now_time,
        ];
        try {
            $date = Carbon::now()->format('Y-m-d');
            $count = RedisHelper::getInstance()->hLen('access_log:'.$date);
            if (empty($count)) {
                $count = 1;
            } else {
                $count += 1;
            }
            $res = RedisHelper::getInstance()->hSet('access_log:'.$date, $count, json_encode($log_data));
            trace('日志信息api_log'.$res);
        } catch (\Exception $exception) {
            trace('日志信息保存失败');
        }
    }
}