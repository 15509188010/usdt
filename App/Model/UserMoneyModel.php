<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/10 0010
 * Time: 上午 10:23
 */

namespace App\Model;


class UserMoneyModel extends Base
{
    public $tableName = "user_money";

    public function insertUserMoney($insert) {
        $result = $this->db->insert($this->tableName,$insert);
        return $result ? $this->db->getInsertId() : null;
    }
}