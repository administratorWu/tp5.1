<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\cache\driver\Redis;
use app\admin\model\User;
use app\admin\model\XgRedis;

class AdminController extends Controller
{
    //String、List、Set、Sorted Set、hashes
    public function test()
    {
        // $redis = new  Redis(config('cache.redis'));
        // $redis->set('name','张三');
        // dump($redis->get('name'));
        $redistest = new XgRedis();
        // 数据缓存到redis
        $post = User::field('id,username,phone,addtime')->select()->toArray();
       
        foreach($post as $k=>$v){
            $newUser = $redistest->show_redis_page_info('userlist',$v['id']);
            if (!$newUser){
                $redistest->set_redis_page_info('userlist',$v['id'],$v);
            }
            
        }
        // dump($redistest->del_redis_page_info('userlist',[52,53]));  //删除redis中的 52 53数据
       
        dump($redistest->get_redis_page_info('userlist',2,2));  //分页
       
        $id = 1;
        dump($redistest->show_redis_page_info('userlist',$id));  //查询单条
       
        // $redistest->clear('db');  //db删除当前数据库数据    all删除所有数据

        // $redistest->vague_del('userlist');  //模糊删除
    }
}
