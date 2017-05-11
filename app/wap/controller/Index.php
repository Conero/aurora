<?php
/**
 * User: Joshua Conero
 * Date: 2017/5/6 0006 12:13
 * Email: brximl@163.com
 * Name: 移动端首页
 */

namespace app\wap\controller;
use app\common\controller\Wap;
use app\common\model\Visit;
class Index extends Wap
{
    public function index()
    {
        $this->loadScript([
            'js'    => ['index/index']
        ]);
        $page = [];
        // 访问分布
        $rdata = (new Visit())->getVisitCount();
        $page['count'] = $rdata['count'];
        $user = $this->getUserInfo('user');
        if($user) $page['user'] = $user;
        $page['isLogin'] = $user? 'Y':'N';
        $this->assign('page',$page);
//        debugOut($this->getUserInfo());
        return $this->fetch();
    }
}
