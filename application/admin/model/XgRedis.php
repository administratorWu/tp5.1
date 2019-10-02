<?php

namespace app\admin\model;

use think\Model;
use think\cache\driver\Redis;

class XgRedis extends Model
{
    protected $_redis;
    // public function __construct($hash_prefix = '')
    // {
    //     $this->_redis = new Redis();
    // }

    /*
     * 添加记录
     * @param $hash_prefix 前缀
     * @param $id 记录id
     * @param $data 数据
     * @return bool 返回值
     */
    public function set_redis_page_info($hash_prefix, $id, $data)
    {
        $redis = new  Redis();

        
        if (!is_numeric($id) || !is_array($data)) return false;
        $hashName = $hash_prefix . '_' . $id;
        
        $redis->hmset($hashName, $data);  //哈希
        $redis->zadd($hash_prefix . '_sort', $id, $id);  //有序集
        return true;
    }

    /*
     * 获取分页数据
     * @param $hash_prefix 前缀
     * @param $page 当前页数
     * @param $pageSize 每页多少条
     * @param $hashName Hash 记录名称
     * @param $SortName Redis SortSet 记录名称
     * @param $redis Redis 对象
     * @param $key 字段数组 不传为取出全部字段
     * @return array
     */
    protected function get_redis_page_info($hash_prefix, $page, $pageSize, $key = array())
    {
        if (!is_numeric($page) || !is_numeric($pageSize)) return false;  //判断是否是数字
        //当page=1时         2时   计算每页取多少条数据   从哪开始到哪结束
        $limit_s = ($page - 1) * $pageSize; //   下标0开始取数据      5
        $limit_e = ($limit_s + $pageSize) - 1; // 下标4结束        9

        $range = $this->_redis->zrange($hash_prefix . '_sort', $limit_s, $limit_e); //指定区间内，带有 score 值(可选)的有序集成员的列表。 
        $count = $this->_redis->zcard($hash_prefix . '_sort'); //统计ScoreSet总数
        $pageCount = ceil($count / $pageSize); //总共多少页
        $pageList = array();
        foreach ($range as $qid) {
            if (count($key) > 0) {
                $pageList[] = $this->_redis->hmget($hash_prefix . '_' . $qid, $key); //获取hash表中所有的数据
            } else {
                $pageList[] = $this->_redis->hgetall($hash_prefix . '_' . $qid); //获取hash表中所有的数据
            }
        }
        $data = array(
            'data' => $pageList, //需求数据
            'page' => array(
                'page' => $page, //当前页数
                'pageSize' => $pageSize, //每页多少条
                'count' => $count, //记录总数
                'pageCount' => $pageCount //总页数
            )
        );
        return $data;
    }

    /*
     * 获取单条记录
     * @param $id id
     * @return array
     */
    protected function show_redis_page_info($hash_prefix, $id)
    {
        $info = $this->_redis->hgetall($hash_prefix . '_' . $id);
        return $info;
    }

    /*
     * 删除记录 单条或多条
     * @param $ids ids 数组形式 [1,2,3]
     * @param $hashName Hash 记录名称
     * @param $SortName Redis SortSet 记录名称
     * @param $redis Redis 对象
     * @return bool
     */
    protected function del_redis_page_info($hash_prefix, $ids)
    {
        if (!is_array($ids)) return false;
        foreach ($ids as $value) {
            $hashName = $hash_prefix . '_' . $value;

            $this->_redis->del($hashName);
            $this->_redis->zrem($hash_prefix . '_sort', $value);
        }
        return true;
    }

    /*
     * 清空数据
     * @param string $type db:清空当前数据库 all:清空所有数据库
     * @return bool
     */
    protected function clear($type = 'db')
    {
        if ($type == 'db') {
            $this->_redis->flushdb();
        } elseif ($type == 'all') {
            $this->_redis->flushall();
        } else {
            return false;
        }
        return true;
    }

    //模糊删除
    protected function vague_del($name)
    {
        $postlist = $this->_redis->keys($name . '*');

        Redis::pipeline(function ($pipe) use ($postlist) {
            foreach ($postlist as $item) {
                $pipe->del($item);
            }
        });
        return true;
    }
}
