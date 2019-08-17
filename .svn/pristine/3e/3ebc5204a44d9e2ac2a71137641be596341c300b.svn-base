<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/13 0013
 * Time: 下午 4:33
 */

namespace App\Crontab;

use EasySwoole\EasySwoole\Crontab\AbstractCronTask;
use App\Utility\Pool\RedisPool;

class UsDoller extends AbstractCronTask
{
    public static function getRule(): string
    {
        // TODO: Implement getRule() method.
        // 定时周期 （每小时）
        return '@hourly';
    }

    public static function getTaskName(): string
    {
        // TODO: Implement getTaskName() method.
        // 定时任务名称
        return 'UsDoller';
    }

    static function run(\swoole_server $server, int $taskId, int $fromWorkerId,$flags=null)
    {
        // TODO: Implement run() method.
        // 定时任务处理逻辑
        //获取美元汇率
        $result = file_get_contents('http://api.k780.com:88/?app=finance.rate&scur=USD&tcur=CNY&appkey=10003&sign=b59bc3ef6191eb9f747dd4e83c99f2a4');

        $data=json_decode($result,true);
        if ($data['success']==1){
            $RedisPool = RedisPool::defer();
            $price=$data['result']['rate'];
            $RedisPool->set('usrate',$price);
        }

        echo 'UsDoller -执行成功';
    }
}