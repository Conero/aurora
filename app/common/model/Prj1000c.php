<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/28 0028 9:23
 * Email: brximl@163.com
 * Name: 项目列表
 */

namespace app\common\model;


use think\Model;

class Prj1000c extends Model
{
    protected $table = 'prj1000c';
    protected $pk = 'listid';

    /**
     * 项目配置项
     * @return \think\model\relation\HasMany
     */
    public function Settings(){
        return $this->hasMany('Prj1001c','pid');
    }
    /**
     * 项目信息发布
     * @return \think\model\relation\HasMany
     */
    public function Inform(){
        return $this->hasMany('Prj1002c','pid');
    }
}