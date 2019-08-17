<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/10 0010
 * Time: 上午 11:22
 */

namespace App\Model;


class MoneyChangeModel extends  Base
{
    public $tableName = "money_change";

    public function insertMoneyChange($insert) {
        $result = $this->db->insert($this->tableName,$insert);
        return $result ? $this->db->getInsertId() : null;
    }

    /**
     * @param $uid
     * @param $lx
     * @return \EasySwoole\Mysqli\Mysqli|mixed
     * @throws \EasySwoole\Mysqli\Exceptions\ConnectFail
     * @throws \EasySwoole\Mysqli\Exceptions\OrderByFail
     * @throws \EasySwoole\Mysqli\Exceptions\PrepareQueryFail
     * @throws \Throwable
     */
    public function  getDmrLog($uid,$lx)
    {
        if (!empty($lx)){
            $this->db->where ("lx", $lx);
        }
        $result=$this->db->where('uid',$uid)->where('type',2)->orderBy('datetime', 'DESC')->get($this->tableName,null,'datetime,money,lx');

        if (!empty($result)){
            foreach ($result as $k => &$v) {
                $v['datetime']=date('m-d i:s',strtotime($v['datetime']));
                if ($v['lx']==1){
                    $v['money']='+'.$v['money'];
                    $v['der']='DMR转入';
                }else{
                    $v['money']='-'.$v['money'];
                    $v['der']='DMR转出';
                }
            }
        }
        return $result;
    }

    /**
     * @param $uid
     * @param $lx
     * @return \EasySwoole\Mysqli\Mysqli|mixed
     * @throws \EasySwoole\Mysqli\Exceptions\ConnectFail
     * @throws \EasySwoole\Mysqli\Exceptions\OrderByFail
     * @throws \EasySwoole\Mysqli\Exceptions\PrepareQueryFail
     * @throws \Throwable
     */
    public function  getUsdtLog($uid,$lx)
    {
        if (!empty($lx)){
            $this->db->where ("lx", $lx);
        }
        $result=$this->db->where('uid',$uid)->where('type',1)->orderBy('datetime', 'DESC')->get($this->tableName,null,'datetime,money,lx');

        if (!empty($result)){
            foreach ($result as $k => &$v) {
                $v['datetime']=date('m-d i:s',strtotime($v['datetime']));
                if ($v['lx']==1){
                    $v['money']='+'.$v['money'];
                    $v['der']='USDT转入';
                }else{
                    $v['money']='-'.$v['money'];
                    $v['der']='USDT转出';
                }
            }
        }
        return $result;
    }
}