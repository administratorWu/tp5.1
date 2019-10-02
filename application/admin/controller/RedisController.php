<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\cache\driver\Redis;
use app\admin\model\User;
use app\admin\model\XgRedis;

class RedisController extends Controller
{
    //String、List、Set、Sorted Set、hashes
    public function test()
    {
        // $redis = new  Redis(config('cache.redis'));
        // $redis->set('name','张三');
        // dump($redis->get('name'));
        $redistest = new XgRedis();
        $post = User::field('id,username,phone,addtime')->select()->toArray();
       
        foreach($post as $k=>$v){
            $redistest->set_redis_page_info('userlist',$v['id'],$v);
        }
        // dd(XgRedis::del_redis_page_info('postlist',[52,53]));  //删除redis中的 52 53数据
       
        // dd(XgRedis::get_redis_page_info('postlist',1,5));  //分页
       
        // $id = 1;
        // dd(XgRedis::show_redis_page_info('postlist',$id));  //查询单条
       
        // XgRedis::clear('db');  //db删除当前数据库数据    all删除所有数据

        // XgRedis::vague_del('postlist');  //模糊删除
    }
}
