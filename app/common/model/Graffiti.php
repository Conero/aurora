<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/6/6 0006 22:47
 * Email: brximl@163.com
 * Name:
 */

namespace app\common\model;


use think\Model;

class Graffiti extends Model
{
    protected $table = 'graffiti';
    protected $pk = 'listid';

    /**
     * 生成主键
     * @return mixed
     */
    public function getPkVal(){
        return getPkValue('pk_graffiti__listid');
    }
}