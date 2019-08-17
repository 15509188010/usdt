<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/11 0011
 * Time: 下午 4:39
 */

namespace App\HttpController\Manage;

use App\HttpController\Base;
use App\Utility\Pool\MysqlPool;
use App\Utility\Pool\RedisPool;
use EasySwoole\Http\Message\Status;

class Usdt extends Base
{
    public $user = [];

    public $page=1;

    public $limit=10;

    protected function onRequest(?string $action): ?bool
    {
        $token = $this->request()->getRequestParam('token');
        $RedisPool = RedisPool::defer();
        $user = $RedisPool->get('Manage_token_' . $token);
        if (!$user) {
            $this->response()->redirect("/Manage/login");
            return false;
        }
        $this->user = json_decode($user, true);
        $page=$this->request()->getRequestParam('page');
        $limit=$this->request()->getRequestParam('limit');
        if (isset($page)){
            $this->page=$page;
        }
        if (isset($limit)){
            $this->limit=$limit;
        }

        return true;
    }

    /**
     * <method> GET
     * <into> 查询usdt转入的订单页面
     */
    public function into()
    {
        return $this->render('Usdt/into');
    }

    /**
     * <orderList> 转入列表
     * @return bool
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     */
    public function orderList()
    {
        $db = MysqlPool::defer();
        $list = $db->where('lx', 1)
            ->where('status', 1)
            ->orderBy('datetime', 'DESC')
            ->get('usdt_order', [($this->page-1)*$this->limit,$this->limit], 'id,uid,ad_usdt,usdt,path,orderid,datetime,path');
        if (empty($list)) {
            return $this->writeJson(10004, '没有数据');
        }
        $count = $db->where('lx', 1)
            ->where('status', 1)
            ->orderBy('datetime', 'DESC')->count('usdt_order',null,'id');
        return $this->writeJson(0, 'SUCCESS', $list, $count);
    }

    /**
     * <pass> POST
     * <pass> 通过usdt转入
     * @params id=>订单id
     * @return bool
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     */
    public function pass()
    {
        $id = $this->request()->getRequestParam('id');
        if (empty($id)) {
            return $this->writeJson(10004, 'id非法!');
        }
        $db = MysqlPool::defer();
        $info = $db->where('id', $id)->where('status', 1)->where('lx', 1)->getOne('usdt_order', 'usdt,uid,orderid');//转入订单信息
        if (empty($info)) {
            return $this->writeJson(10004, '没有找到订单!');
        }
        $db->startTransaction();
        /*****************************修改转入订单状态**********************************/
        $order_up = [
            'status' => 2,//通过审核
            'datetime' => date('Y-m-d H:i:s'),
            'operator' => $this->user['id'],//审核人id
        ];
        $res1 = $db->where('id', $id)->update('usdt_order', $order_up);

        /******************************增加转入人账户+usdt余额****************************/
        $m_user = $db->where('uid', $info['uid'])->getOne('user_money', 'usdt,u_onway');
        $m_up = [
            'usdt' => bcadd($m_user['usdt'], $info['usdt'], 4),//+usdt余额
        ];
        $res2 = $db->where('uid', $info['uid'])->update('user_money', $m_up);

        if ($res1 && $res2) {
            /*****************写流水转入记录***********************/
            $orderid = $this->createId($info['uid'], $info['usdt']);//订单号
            $allMoney=bcadd($m_user['usdt'],$m_user['u_onway']);//总
            $add = [
                'uid' => $info['uid'],//用户id
                'ymoney' => $allMoney,//原总
                'money' => $info['usdt'],//变动数量
                'gmoney' => bcadd($allMoney,$info['usdt'],4),//变动后
                'datetime' => date('Y-m-d H:i:s'),
                'transid' => $orderid,//流水号
                'orderid' => $info['orderid'],//对应order表
                'lx' => 1,//转入
                'type' => 1,//1 usdt
                't' => 1,//结算方式,非实时
            ];
            $db->insert('money_change', $add);
            $db->commit();
            return $this->writeJson(Status::CODE_OK, 'SUCCESS');
        } else {
            $db->rollback();
            return $this->writeJson(10004, '操作失败!');
        }
    }

    /**
     * <methid> POST
     * <delEdit> 驳回转入编辑理由页面
     * @return bool
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     */
    public function delEdit()
    {
        $id = $this->request()->getRequestParam('id');
        if (empty($id)) {
            return $this->writeJson('10004', 'ERROR');
        }
        $db = MysqlPool::defer();
        $info = $db->where('id', $id)->getOne('usdt_order', 'usdt,id,orderid');
        return $this->render('Usdt/delEdit', ['data' => $info]);
    }

    /**
     * <method> POST
     * <del> 驳回转入usdt订单
     * @return bool
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     */
    public function del()
    {
        $id = $this->request()->getRequestParam('id');
        $content = $this->request()->getRequestParam('memo');
        if (empty($id)) {
            return $this->writeJson(10004, 'ERROR');
        }
        if (empty($content)) {
            return $this->writeJson(10004, '请输入驳回理由');
        }
        $db = MysqlPool::defer();
        $exist = $db->where('id', $id)->where('status', 1)->where('lx', 1)->getOne('usdt_order', 'id');
        if (empty($exist)) {
            return $this->writeJson(10004, '订单检索失败!');
        }
        $up = [
            'datetime' => date('Y-m-d H:i:s'),
            'operator' => $this->user['id'],//审核人员id
            'status' => 3,//驳回转入
            'contentstr' => $content,//驳回理由
        ];
        $res = $db->where('id', $id)->update('usdt_order', $up);
        if ($res) {
            return $this->writeJson(Status::CODE_OK, '操作成功');
        }

        return $this->writeJson(10004, '操作失败!');

    }

    /**
     * <method> GET
     * <out> usdt转出页面
     */
    public function out()
    {
        return $this->render('Usdt/out');
    }

    /**
     * <method> GET
     * <orderOutList> USDT转出列表
     * @return bool
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     */
    public function orderOutList()
    {
        $db = MysqlPool::defer();
        $list = $db->where('lx', 2)
            ->where('status', 1)
            ->orderBy('datetime', 'DESC')
            ->get('usdt_order', [($this->page-1)*$this->limit,$this->limit], 'id,uid,ad_usdt,usdt,path,ser_money,orderid,datetime');
        if (empty($list)) {
            return $this->writeJson(10004, '没有数据');
        }
        $count = $db->where('lx', 2)
            ->where('status', 1)
            ->orderBy('datetime', 'DESC')->count('usdt_order',null,'id');
        return $this->writeJson(0, 'SUCCESS', $list, $count);
    }

    /**
     * <method> POST
     * <passOut> 通过usdt转出
     * @return bool
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     */
    public function passOut()
    {
        $id = $this->request()->getRequestParam('id');
        if (empty($id)) {
            return $this->writeJson(10004, 'id不能为空');
        }
        $db = MysqlPool::defer();
        $order = $db->where('id', $id)->where('lx', 2)->where('status', 1)->getOne('usdt_order', 'usdt,orderid,uid,ser_money');
        if (empty($order)) {
            return $this->writeJson(10004, '检索订单失败!');
        }

        $db->startTransaction();
        /****************************修改转出订单状态********************************/
        $order_up = [
            'status' => 2,//审核通过
            'operator' => $this->user['id'],//审核人员id
            'datetime' => date('Y-m-d H:i:s'),
        ];
        $res1 = $db->where('id', $id)->update('usdt_order', $order_up);

        /******************************减用户在途的金额*****************************/
        $m_user = $db->where('uid', $order['uid'])->getOne('user_money', 'usdt,u_onway');//获取在途
        $m_up = [
            'u_onway' => bcsub($m_user['u_onway'], $order['usdt'], 4),
        ];
        $res2=$db->where('uid',$order['uid'])->update('user_money',$m_up);

        if ($res1 && $res2){
            /*******************************添加流水记录*****************************/
            $orderid = $this->createId($order['uid'], $order['usdt']);//订单号
            $allMoney=bcadd($m_user['usdt'],$m_user['u_onway'],4);//原总
            $add=[
                'uid'=>$order['uid'],//用户id
                'ymoney'=>$allMoney,//总
                'money'=>$order['usdt'],//变动
                'gmoney'=>bcsub($allMoney, $order['usdt'], 4),//变动后
                'datetime'=>date('Y-m-d H:i;s'),
                'ser_money'=>$order['ser_money'],//usdt转出手续费
                'transid'=>'OUT'.$orderid,//流水号
                'lx'=>2,//转出
                'type'=>1,//usdt
                'orderid'=>$order['orderid'],//订单号
                't'=>1,//结算方式,非即时
            ];
            $db->insert('money_change',$add);

            /*********************************添加品台收入记录*********************************/
            $this->editMoney($order['ser_money'],'USDT',$order['uid']);//平台收入核心
            $db->commit();
            return $this->writeJson(Status::CODE_OK,'SUCCESS');
        }else{
            $db->rollback();
            return $this->writeJson(10004,'操作失败');
        }

    }

    /**
     * <method> GET
     * <delOutEdit> 驳回usdt转出页面
     * @return bool
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     */
    public function  delOutEdit()
    {
        $id = $this->request()->getRequestParam('id');
        if (empty($id)) {
            return $this->writeJson('10004', 'ERROR');
        }
        $db = MysqlPool::defer();
        $info = $db->where('id', $id)->getOne('usdt_order', 'usdt,id,orderid');
        return $this->render('Usdt/delOutEdit', ['data' => $info]);
    }

    /**
     * <method> POST
     * <delOut> 驳回usdt转出
     * @return bool
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     */
    public function delOut()
    {
        $id = $this->request()->getRequestParam('id');
        $content = $this->request()->getRequestParam('memo');

        if (empty($id)){
            return $this->writeJson('10004', 'ERROR');
        }
        if (empty($content)){
            return $this->writeJson('10004', '驳回转出原因!');
        }
        $db = MysqlPool::defer();
        $order=$db->where('id',$id)->where('status',1)->where('lx',2)->getOne('usdt_order','uid,usdt,orderid');//订单
        if (empty($order)){
            return $this->writeJson(10004,'检索订单失败!');
        }

        $db->startTransaction();
        /****************************修改订单状态************************************/
        $order_up=[
            'datetime'=>date('Y-m-d H:i:s'),
            'contentstr'=>$content,//驳回转出原因
            'status'=>3,//驳会
            'operator'=>$this->user['id'],//审核人员id
        ];
        $res1=$db->where('id',$id)->update('usdt_order',$order_up);

        /*****************************将在途usdt返回余额*****************************/
        $m_user=$db->where('uid',$order['uid'])->getOne('user_money','usdt,u_onway,uid');
        $m_up=[
            'u_onway'=>bcsub($m_user['u_onway'],$order['usdt'],4),//减在途
            'usdt'=>bcadd($m_user['usdt'],$order['usdt'],4),//usdt余额
        ];

        $res2=$db->where('uid',$order['uid'])->update('user_money',$m_up);

        if ($res1 && $res2){
            /***************************写流水记录*************************************/
            $orderid = $this->createId($order['uid'], $order['usdt']);//订单号
            $allMoney=bcadd($m_user['usdt'],$m_user['u_onway']);//原总
            $add=[
                'uid'=>$order['uid'],//用户id
                'ymoney'=>$allMoney,//原总
                'money'=>$order['usdt'],//变动金额
                'gmoney'=>bcadd($allMoney,$order['usdt'],4),//变动后
                'datetime'=>date('Y-m-d H:i:s'),
                'transid'=>'OUT'.$orderid,//流水号
                'lx'=>2,//转出
                'type'=>1,//usdt
                'orderid'=>$order['orderid'],//系统订单号
                'contentstr'=>$content,//备注
                't'=>1,//结算时间,非即时
            ];
            $db->insert('money_change',$add);
            $db->commit();
            return $this->writeJson(Status::CODE_OK,'SUCCESS');
        }else{
            $db->rollback();
            return $this->writeJson(10004,'操作失败!');
        }
    }

    /**
     * <method> GET
     * <usdtLog> USDT流水记录页面
     */
    public function usdtLog()
    {
        return $this->render('Usdt/usdtLog');
    }


    /**
     * <method> POST
     * <usdtLogList> usdt流水
     * @return bool
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     */
    public function usdtLogList()
    {
        $db = MysqlPool::defer();
        $list=$db->where('status',2)
            ->whereOr('status',3)->orderBy('datetime','DESC')
            ->get('usdt_order',[($this->page-1)*$this->limit,$this->limit],'id,uid,usdt,orderid,ad_usdt,lx,operator,contentstr,status,datetime');
        if (empty($list)){
            return $this->writeJson(10004,'没有数据');
        }
        $count=$db->where('status',2)
            ->whereOr('status',3)->orderBy('datetime','DESC')->count('usdt_order',null,'id');
        foreach ($list as $k => &$v){
            //转入还是转出
            if ($v['lx']==1){
                $v['lx']='转入';
            }else{
                $v['lx']='转出';
            }
            //2=>通过 3=>驳回
            if ($v['status']==2){
                $v['status']='通过';
            }else if($v['status']==3){
                $v['status']='<a style="color: #B22D00">驳回</a>';
            }
            //备注
            if (empty($v['contentstr'])){
                $v['contentstr']='---';
            }
        }
        return $this->writeJson(0,'SUCCESS', $list, $count);
    }

    /**
     * <method> GET
     * <dmrLog> dmr流水记录页面
     */
    public function dmrLog()
    {
        return $this->render('Usdt/dmrLog');
    }

    /**
     * <method> POST
     * <dmrLogList> dmr的交易流水
     * @return bool
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     */
    public function dmrLogList()
    {
        $db = MysqlPool::defer();
        $list=$db->where('status',2)
            ->orderBy('datetime','DESC')
            ->get('dmr_order',[($this->page-1)*$this->limit,$this->limit],'id,uid,to_uid,datetime,orderid,dmr,d_frozen,ad_dmr,status,ser_money,ac_money');
        if (empty($list)){
            return $this->writeJson(10004,'没有相关数据');
        }
        $count=$db->where('status',2)
            ->orderBy('datetime','DESC')->count('dmr_order',null,'id');
        foreach ($list as $k => &$v){
            $v['status']='<a style="color: #0bb20c">成功</a>';
        }
        return $this->writeJson(0,'SUCCESS',$list,$count);
    }

    /**
     * <method> POST
     * <changeLog> 资金变动记录页面
     */
    public function changeLog()
    {
        return $this->render('Usdt/changeLog');
    }

    /**
     * <method> POST
     * <changeLogList> 总资金流动记录
     * @return bool
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     */
    public function changeLogList()
    {
        $db = MysqlPool::defer();
        $list=$db->orderBy('datetime','DESC')
            ->get('money_change',[($this->page-1)*$this->limit,$this->limit],'id,uid,ymoney,money,gmoney,datetime,transid,lx,type,contentstr,t');
        if (empty($list)){
            return $this->writeJson(10004,'没有相关数据');
        }
        $count=$db->orderBy('datetime','DESC')->count('money_change',null,'id');
        foreach ($list as $k => &$v){
            if (empty($v['contentstr'])){
                $v['contentstr']='---';//备注
            }
            if ($v['lx']==1){
                $v['lx']='转入';
            }else if($v['lx']==2){
                $v['lx']='转出';
            }
            if($v['type']==1){
                $v['type']='USDT';
            }else{
                $v['type']='DMR';
            }
            if ($v['t']==0){
                $v['t']='实时到账';
            }else{
                $v['t']='预约到账';
            }
        }
        return $this->writeJson(0,'SUCCESS',$list,$count);
    }

    /**
     * <method> GET
     * <frozenLog> 解冻记录页面
     */
    public function frozenLog()
    {
        return $this->render('Usdt/frozenLog');
    }

    /**
     * <method> POST
     * <frozenLogList> 解冻记录列表
     * @return bool
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     */
    public function frozenLogList()
    {
        $db = MysqlPool::defer();
        $count=$db->count('d_frozen_log',null,'id');
        $list=$db->orderBy('datetime','DESC')->get('d_frozen_log',[($this->page-1)*$this->limit,$this->limit],'id,uid,d_frozen,ratio,money,datetime');
        return $this->writeJson(0,'SUCCESS',$list,$count);
    }

    /**
     * OverWrite
     * @param int $statusCode
     * @param null $msg
     * @param null $result
     * @param int $count
     * @return bool
     */
    protected function writeJson($statusCode = 200, $msg = null, $result = null, $count = 0)
    {
        if (!$this->response()->isEndResponse()) {
            $data = [
                "code" => $statusCode,
                "data" => $result,
                "msg" => $msg,
                "count" => $count,
            ];
            $this->response()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
            $this->response()->withHeader('Access-Control-Allow-Origin', "*");
            $this->response()->withStatus(200);//http响应码
            return true;
        } else {
            return false;
        }
    }
}