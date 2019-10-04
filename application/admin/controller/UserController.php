<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\model\User;
use think\facade\Cache;
use app\admin\model\XgRedis;
use think\cache\driver\Redis;

class UserController extends Controller
{
    public function index()
    {
        $redis = new XgRedis();
        $list = User::field('id,username,phone,true_status,status,addtime')->order('id desc')->paginate(15);
        
        $arr = $list->toArray();//转换为数组
        foreach ($arr['data'] as $k => $v) {
            $newUser =  $redis->show_redis_page_info('userlist',$v['id']);
            if (!$newUser){
                $redis->set_redis_page_info('userlist',$v['id'],$v);
            }
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

    public function amenduser(Request $request)
    {
        if ($request->isGet()) {
            $id = $request->get('id');
            $redis = new XgRedis();
            $userinfo = $redis->show_redis_page_info('userlist',$id);
            if (!$userinfo) {
                $userinfo = User::where(['id'=>$id])->field('id,username,phone,true_status,status,addtime')->find()->toArray();
                $rediss = $redis->set_redis_page_info('userlist',$userinfo['id'],$userinfo);
            }
            return view('admin/user/amenduser',['title'=>'修改会员','user'=>$userinfo]);
        }

        if ($request->isAjax()) {
            
            $data = $request->post();
            
            if ($data['password']) {
                $data['password'] = md5($data['password']);
            }else{
                unset($data['password']);
            }
            $userinfo = User::where(['id'=>$data['id']])->field('id,username,phone,true_status,status,addtime')->find()->toArray();
            
            if ($data['phone'] != $userinfo['phone']) {
                $user = new User();
                $checkphone = $user->checkPhone($data['phone']);
                if (!$checkphone){
                    return json(['status'=>-1,'msg'=>'手机号已注册']);
                }
            }
            $amend = User::where(array('id'=>$data['id']))->update($data);
            if ($amend) {
                $redis = new XgRedis();
                $redis->del_redis_page_info('userlist',array($data['id']));
                $userinfo = User::where(['id'=>$data['id']])->field('id,username,phone,true_status,status,addtime')->find()->toArray();
                $redis->set_redis_page_info('userlist',$userinfo['id'],$userinfo);
                return json(['status'=>1,'msg'=>'操作成功']); 
            }
            return json(['status'=>-1,'msg'=>'操作失败']);
        }
    }
}
