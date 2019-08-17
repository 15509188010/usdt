<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/13 0013
 * Time: 上午 9:13
 */

namespace App\Crontab;

use EasySwoole\EasySwoole\Crontab\AbstractCronTask;
use App\Utility\Pool\MysqlPool;


class Unfreeze extends AbstractCronTask
{
    public static function getRule(): string
    {
        // TODO: Implement getRule() method.

        return '40 15 * * *';//每天15点执行
    }

    public static function getTaskName(): string
    {
        // TODO: Implement getTaskName() method.
        // 定时任务名称
        return 'Unfreeze';
    }

    static function run(\swoole_server $server, int $taskId, int $fromWorkerId, $flags = null)
    {
        echo '执行';
        // TODO: Implement run() method.
        // 定时任务处理逻辑
        $db = MysqlPool::defer();

        //获取DMR配置->解冻的比率
        $config = $db->where('type', 'DMR')->getOne('config', 'ratio');
        if (empty($config)) {
            return false;
        }
        //获取用户账户表
        $list = $db->get('user_money', null, 'uid,dmr,d_frozen');
        if (empty($list)) {
            return false;
        }

        //遍历账户数据
        foreach ($list as $k => $v) {
            if ($v['d_frozen'] != 0) {
                //释放金额
                $release=bcmul($v['d_frozen'],$config['ratio'],4);//按冻结部分总量解冻
                $up = [
                    'dmr'=>bcadd($v['dmr'],$release,4),//dmr余额增加
                    'd_frozen'=>bcsub($v['d_frozen'],$release,4),//冻结减少
                ];
                $res=$db->where('uid',$v['uid'])->update('user_money',$up);//修改用户账户
                if ($res){
                    //写流水记录
                    $add=[
                        'uid'=>$v['uid'],//用户id
                        'd_frozen'=>$v['d_frozen'],//解冻前
                        'money'=>$release,//本次解冻数量
                        'ratio'=>$config['ratio'],//解冻比率
                        'datetime'=>date('Y-m-d H:i:s'),
                    ];
                    $db->insert('d_frozen_log',$add);//添加记录
                }else{
                    //解冻异常,发送通知
                }
            }
        }

        return true;
    }
}