<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/6/5 0005 20:58
 * Email: brximl@163.com
 * Name: 文章类型
 */

namespace app\common\model;


use think\Model;

class Atc1000c extends Model
{
    protected $table = 'atc1000c';
    protected $pk = 'listid';

    /**
     * 获取文集名称
     * @param null $uid 用户刷选
     * @return array
     */
    public function getCollecteds($uid=null){
        $where = $uid? ['uid'=>$uid]:null;
        $data = $this->db()
            ->field('collected,count(collected) as ctt')
            ->where($where)
            ->group('collected')
            ->order('date desc')
            ->select();
        $collected = [];
        foreach ($data as $v){
            $collected[$v['collected']] = $v['ctt'];
        }
        return $collected;
    }

    /**
     * 获取署名
     * @param $uid
     * @return array
     */
    public function getSigns($uid){
        $where = $uid? ['uid'=>$uid]:null;
        $data = $this->db()
            ->field('sign,count(sign) as ctt')
            ->where($where)
            ->group('sign')
            ->order('date desc')
            ->select();
        $collected = [];
        foreach ($data as $v){
            $collected[$v['sign']] = $v['ctt'];
        }
        return $collected;
    }

    /**
     * 文章评论
     * @return \think\model\relation\HasMany
     */
    public function Comments(){
        return $this->hasMany('Atc1002c','pid');
    }
}