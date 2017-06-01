<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/6/1 0001 22:05
 * Email: brximl@163.com
 * Name: 系统分组
 */

namespace app\common\model;


use think\Model;

class Group extends Model
{
    protected $pk = 'listid';
    /**
     * 分组下的用户
     * @return \think\model\relation\HasMany
     */
    public function roles(){
        return $this->hasMany('Role','pid');
    }
    public function GoJsTreeNode(){
        $data = $this->db()
            ->select();
        $retVal = [];
        foreach ($data as $v){
            $pid = $v['listid'];
            $key = $v['code'];
            $retVal[] = [
                'key' => $key,
                'text'=> $v['descrip'],
                'isGroup' => true
            ];
            $role = new Role();
            $childNode = $role->where('pid',$pid)
                ->select();
            foreach ($childNode as $vv){
                $retVal[] = [
                    'key'  => $vv['code'],
                    'text' => $vv['descrip'],
                    'group' => $key
                ];
            }
        }
        return $retVal;
    }
    public function JsTreeData(){
        $data = $this->db()
            ->select();
        $retVal = [];
        foreach ($data as $v){
            $pid = $v['listid'];
            $key = $v['code'];
            $retVal[] = [
                'id' => $key,
                'parent' => "#",
                'text'=> $v['descrip'],
                'type' => 'group'
            ];
            $role = new Role();
            $childNode = $role->where('pid',$pid)
                ->select();
            foreach ($childNode as $vv){
                $retVal[] = [
                    'id'  => $vv['code'],
                    'text' => $vv['descrip'],
                    'parent' => $key,
                    'type' => 'role'
                ];
            }
        }
        return $retVal;
    }
}