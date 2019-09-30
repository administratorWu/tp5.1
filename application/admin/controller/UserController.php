<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('admin/user/list',['title'=>'会员列表']);
    }
}
