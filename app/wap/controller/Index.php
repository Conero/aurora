<?php
/**
 * User: Joshua Conero
 * Date: 2017/5/6 0006 12:13
 * Email: brximl@163.com
 * Name: 移动端首页
 */

namespace app\wap\controller;
use app\common\controller\Wap;
class Index extends Wap
{
    public function index()
    {
        $page = [];
        $page['count'] = $this->getVisitCount();
        $this->assign('page',$page);
        return $this->fetch();
    }
}
