<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/3 0003
 * Time: 下午 5:13
 */

namespace App\WebSocket;

use EasySwoole\Socket\AbstractInterface\Controller;
use App\Utility\Pool\RedisPool;
use App\Utility\Pool\MysqlPool;

/**
 * 留言
 * Class Leaving
 * @package App\WebSocket
 */
class Leaving extends Controller
{
    public function leavingMessage()
    {
        $info = $this->caller()->getArgs();
        $info = $info['data'];
        $RedisPool = RedisPool::defer();
        $user = $RedisPool->get('User_token_' . $info['token']);
        $user = json_decode($user, true);
        if ($user == null) {
            $data = [
                "type" => "token expire",
            ];
            $this->response()->setMessage(json_encode($data));
            return;
        }
        $db = MysqlPool::defer();
        //收到留言的数据包
        //记录到留言记录表
        //查询用户表用户是否在线
        //在线直接推送
        //不在线离线推送
    }
}