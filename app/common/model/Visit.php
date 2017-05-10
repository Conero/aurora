<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/10 0010 22:46
 * Email: brximl@163.com
 * Name: 访问你统计
 */

namespace app\common\model;


use think\Model;

class Visit extends Model
{
    protected $pk = 'listid';

    /**
     * 获取大盖统计量
     * @return array
     */
    public function getVisitCount(){
        $ctt = $this->db()->count();
        $mctt = $this->db()->where('is_mobile','Y')->count();
        $wctt = $ctt - $mctt;
        return [
            'count' => $ctt,
            'mcount'=> $mctt,
            'wcount'=> $wctt
        ];
    }
}