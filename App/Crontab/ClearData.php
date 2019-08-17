<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/13 0013
 * Time: 下午 4:54
 */

namespace App\Crontab;

use EasySwoole\EasySwoole\Crontab\AbstractCronTask;
use App\Utility\Pool\RedisPool;


class ClearData extends AbstractCronTask
{
    public static function getRule(): string
    {
        // TODO: Implement getRule() method.
        // 定时周期 （每小时）
        return '55 23 * * *';//每天23:55执行
    }

    public static function getTaskName(): string
    {
        // TODO: Implement getTaskName() method.
        // 定时任务名称
        return 'ClearData';
    }

    static function run(\swoole_server $server, int $taskId, int $fromWorkerId,$flags=null)
    {
        // TODO: Implement run() method.
        // 定时任务处理逻辑
        //保存最后一次的usdt价格和dmr价格
        $RedisPool=RedisPool::defer();
        $dmr_price_24=$RedisPool->rPop('dmr-list');
        $RedisPool->set('dmr_price_24',$dmr_price_24);

        $usdt_price_24=$RedisPool->rPop('usdt-list');
        $RedisPool->set('usdt_price_24',$usdt_price_24);

        //删除数据
        $RedisPool->del('dmr-list');
        $RedisPool->del('usdt-list');

        echo '我在23:55执行成功的';
    }
}