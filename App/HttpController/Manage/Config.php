<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/13 0013
 * Time: 上午 10:19
 */

namespace App\HttpController\Manage;


use App\HttpController\Base;
use App\Utility\Pool\MysqlPool;
use App\Utility\Pool\RedisPool;
use EasySwoole\Http\Message\Status;
use EasySwoole\Validate\Validate;

class Config extends  Base
{
    public $user = [];

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
        return true;
    }

    /**
     * <method> GET
     * <setDmr> 设置dmr页面
     */
    public function setDmr()
    {
        $db = MysqlPool::defer();
        $data=$db->where('type','DMR')->getOne('config','ratio,t,min,max,out_rate,dmr_price');
        if(empty($data)){
            return $this->writeJson(10004,'没有相关数据');
        }
        return $this->render('Config/setDmr',['data'=>$data]);
    }


    /**
     * <method> POST
     * <saveDmrConfig> 保存解冻设置
     * @return bool
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     */
    public function saveDmrConfig()
    {
        $validate = new Validate();
        $validate->addColumn('ratio')->required('比率必填');
        $validate->addColumn('t')->required('解冻周期必填');
        $validate->addColumn('min')->required('最小解冻数量');
        $validate->addColumn('max')->required('最大解冻数量');
        $validate->addColumn('out_rate')->required('转出费率');
        $validate->addColumn('dmr_price')->required('dmr价格必填');
        if ($this->validate($validate)){
            $params = $this->request()->getRequestParam();
            $db = MysqlPool::defer();
            $up=[
                'ratio'=>$params['ratio'],//比率
                't'=>$params['t'],//周期
                'min'=>$params['min'],//最小
                'max'=>$params['max'],//最大
                'datetime'=>date('Y-m-d H:i:s'),
                'operator'=>$this->user['id'],
                'out_rate'=>$params['out_rate'],//转出费率
                'dmr_price'=>$params['dmr_price'],//dmr价格
            ];
            $res=$db->where('type','DMR')->update('config',$up);
            if ($res){
                $RedisPool = RedisPool::defer();
                $RedisPool->rPush('dmr-list',$params['dmr_price']);
                return $this->writeJson(Status::CODE_OK,'SUCCESS');
            }
            return $this->writeJson(10004,'操作失败');
        }else{
            return $this->writeJson(10004, $validate->getError()->__toString());
        }

    }

    /**
     * <method> GET
     * <setUsdt> 设置usdt页面
     */
    public function setUsdt()
    {
        $RedisPool = RedisPool::defer();
        $ad_usdt=$RedisPool->get('ad_usdt');
        $usdt_rate=$RedisPool->get('usdt_rate');
        return $this->render('Config/setUsdt',['data'=>['ad_usdt'=>$ad_usdt,'usdt_rate'=>$usdt_rate]]);
    }

    /**
     * <method> POST
     * <saveUsdtConfig> 保存usdt地址
     * @return bool
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     */
    public function saveUsdtConfig()
    {
        $ad_usdt = $this->request()->getRequestParam('ad_usdt');//usdt地址
        $usdt_rate = $this->request()->getRequestParam('usdt_rate');//usdt转出费率

        if (empty($ad_usdt)){
            return $this->writeJson(10004,'地址不能为空');
        }
        if (empty($usdt_rate)){
            return $this->writeJson(10004,'费率不能为空');
        }

        if ($usdt_rate <= 0){
            return $this->writeJson(10004,'费率不合法');
        }
        $RedisPool = RedisPool::defer();
        $res=$RedisPool->set('ad_usdt',$ad_usdt);
        $RedisPool->set('usdt_rate',$usdt_rate);
        if ($res){
           return $this->writeJson(Status::CODE_OK,'SUCCESS');
        }

        return $this->writeJson(10004,'操作失败');
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