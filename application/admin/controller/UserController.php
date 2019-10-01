<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class UserController extends Controller
{
    public function index()
    {
       return view('admin/base/common',['title'=>'公共模板']);
    }
}
