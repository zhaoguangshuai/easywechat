<?php
/**
 * Created by PhpStorm.
 * User: long
 * Date: 2018/8/17
 * Time: 16:05
 */

namespace app\loan\behavior;

use think\Db;

class MongoLog
{

    /**
     * 记录日志
     * @param $data
     */
    public function run()
    {
        $now_time     = time();
        $mongo_config = config('mongodb.MONGO');
        $log_data     = [
            'method'      => request()->controller() . '/' . request()->action(),
            'user_id'     => request()->post('user_id','0'),
            'header_data' => (request()->header()),
            'param_data'  => (request()->param()),
            'add_date'    => date('Y-m-d H:i:s', $now_time),
            'add_time'    => $now_time,
        ];

        try {
            $res = Db::connect($mongo_config)->name('api_log')->insert($log_data);
            trace('mongodb记录访问日志'.$res);
        } catch (\Exception $exception) {
            trace('mongodb记录插入错误信息'.$exception->getMessage());
        }
    }
}