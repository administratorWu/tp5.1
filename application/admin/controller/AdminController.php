<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\cache\driver\Redis;
use app\admin\model\User;
use app\admin\model\XgRedis;

class AdminController extends Controller
{
    public function addAdmin()
    {
        $arr = array(1,2,3);
       dump($arr);
        echo 1;
    }
}
