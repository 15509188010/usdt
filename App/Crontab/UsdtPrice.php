<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/13 0013
 * Time: 下午 3:56
 */

namespace App\Crontab;


use EasySwoole\EasySwoole\Crontab\AbstractCronTask;
use App\Utility\Pool\RedisPool;

class UsdtPrice extends AbstractCronTask
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
        return 'UsdtPrice';
    }

    static function run(\swoole_server $server, int $taskId, int $fromWorkerId,$flags=null)
    {
        // TODO: Implement run() method.
        // 定时任务处理逻辑
        $result = file_get_contents('https://otc-api.eiijo.cn/v1/data/trade-market?coinId=2&currency=1&tradeType=sell&currPage=1&payMethod=0&country=37&blockType=general&online=1&range=0&amount=');

        $data=json_decode($result,true);
        if ($data['code']==200){
            $RedisPool = RedisPool::defer();
            $price=$data['data'][4]['price'];
            $RedisPool->rPush('usdt-list',$price);
        }
        echo 'UsdtPrice -执行成功';
    }
}