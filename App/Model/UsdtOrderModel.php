<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/10 0010
 * Time: 下午 2:57
 */

namespace App\Model;


class UsdtOrderModel extends Base
{
    public $tableName = "usdt_order";

    public function insertUsdtOrder($insert) {
        $result = $this->db->insert($this->tableName,$insert);
        return $result ? $this->db->getInsertId() : null;
    }
}