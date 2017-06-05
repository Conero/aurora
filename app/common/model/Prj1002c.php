<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/6/3 0003 23:47
 * Email: brximl@163.com
 * Name: 项目信息发布数据表
 */

namespace app\common\model;


use think\Model;

class Prj1002c extends Model
{
    protected $table = 'prj1002c';
    protected $pk = 'listid';

    /**
     * 获取消息列表
     * @param $code
     * @param bool $isPid
     * @param function $callback 回调函数
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getInfos($code,$isPid=false,$callback=null){
        $where = ['push_mk'=>'Y'];
        if($isPid) $where['pid'] = $code;
        else{
            $prj = new Prj1000c();
            $where['pid'] = $prj->where('code',$code)->value('listid');
        }
        if($callback && ($callback instanceof \Closure)){
            return call_user_func($callback,
                $this->db()
                ->where($where)
                ->order('push_time desc,mtime'));
        }
        return $this->db()
            ->where($where)
            ->order('push_time desc,mtime')
            ->select()
            ;
    }
}