<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/6/22 0022 21:33
 * Email: brximl@163.com
 * Name: 财务计划-模型
 */

namespace app\common\model;


use think\Model;

class Fnc2000c extends Model
{
    protected $table = 'fnc2000c';
    protected $pk = 'plan_no';
    // 获取主键
    public function getNoVal(){
        return getPkValue('pk_fnc2000c__plan_no');
    }
}