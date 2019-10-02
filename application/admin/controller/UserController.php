<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\model\User;
use think\facade\Cache;

class UserController extends Controller
{
    public function index()
    {
        
        $list = User::field('id,username,phone,true_status,status,addtime')->order('id desc')->paginate(15);
        foreach ($list as $k => $v) {
            Cache::store('redis')->set('user_id='.$v['id'],$v);
        }
        
        return view('admin/user/userlist', ['title' => '会员列表', 'list' => $list]);
    }

    public function adduser(Request $request)
    {
        if ($request->isGet()) {
            return view('admin/user/adduser', ['title' => '添加会员']);
        }
        if ($request->isAjax()) {
            $data = $request->post();
            $user = new User();
            $ifPhone = $user->checkPhone($data['phone']);
            if (!$ifPhone) {
                return json(['status' => -1, 'msg' => '手机号已注册']);
            }
            $ifUserName = $user->checkUserName($data['username']);
            if (!$ifUserName) {
                return json(['status' => -1, 'msg' => '昵称已存在']);
            }
            $data['addtime'] = time();
            $data['password'] = md5($data['password']);
            $adduser = $user->addUser($data);
            if (!$adduser) {
                return json(['status' => -1, 'msg' => '添加失败']);
            }
            return json(['status' => 1, 'msg' => '添加成功']);
        }
    }
}
