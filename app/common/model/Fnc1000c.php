<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/6/22 0022 0:07
 * Email: brximl@163.com
 * Name:
 */

namespace app\common\model;


use think\Model;

class Fnc1000c extends Model
{
    protected $table = 'fnc1000c';
    protected $pk = 'no';
    // 获取主键
    public function getNoVal(){
        return getPkValue('pk_fnc1000c__no');
    }
}