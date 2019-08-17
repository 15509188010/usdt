<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/9 0009
 * Time: 下午 2:09
 */

namespace App\Model;


class UsdtUserModel extends Base
{
    public $tableName = "usdt_user";

    public function getUserByUsername($username) {

        if(empty($username)) {
            return [];
        }

        $this->db->where ("username", $username);
        $result = $this->db->getOne($this->tableName);
        return $result ?? [];
    }

    public function insertUser($insert) {
        $result = $this->db->insert($this->tableName,$insert);
        return $result ? $this->db->getInsertId() : null;
    }

    public function saveData($save,$key)
    {
        $result=$this->db->where('username',$key)->update($this->tableName, $save);
        return $result ?? false;
    }
}