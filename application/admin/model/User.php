<?php

namespace app\admin\model;

use think\Model;

class User extends Model
{
    //
    protected $table = 'test_user';
    protected $pk = 'id';

    public function checkPhone($phone)
    {
        $whyPhone = User::where(array('phone'=>$phone))->find();
        if ($whyPhone){
            return false;
        }
        return true;
    }

    public function checkUserName($username)
    {
        $whyUserName = User::where(array('username'=>$username))->find();
        if ($whyUserName){
            return false;
        }
        return true;
    }

    public function addUser($arr=array())
    {
        if (count($arr) < 1){
            return false;
        }
        $add = User::insert($arr);
        if($add){
            return true;
        }
        return false;
    }

    public function checkList($fields,$where = array()){
        if (count($where) < 1){
            $where = [];   
        }
        $list = User::where($where)->field($fields)->select();
        return $list;
    }
}
