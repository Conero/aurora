<?php
/**
 * User: Joshua Conero
 * Date: 2017/5/6 0006 12:13
 * Email: brximl@163.com
 * Name: 移动端首页
 */

namespace app\wap\controller;
use app\common\model\Prj1001c;
use think\Config;
use app\common\controller\Wap;
use app\common\model\Visit;
class Index extends Wap
{
    // 首页
    public function index()
    {
        $this->loadScript([
            'js'    => ['index/index']
        ]);
        $page = [];
        // 访问分布
        $rdata = (new Visit())->getVisitCount();

        $page['count'] = $rdata['count'];
        $page['dcount'] = $rdata['dcount'];
        $oldt = Config::get('setting.online_date');
        $page['online_cttdt'] = getDays(date('Y-m-d'),$oldt);
        $page['online_dt'] = $oldt;

        $user = $this->getUserInfo('user');
        if($user) $page['user'] = $user;
        $page['isLogin'] = $user? 'Y':'N';
        $this->assign('page',$page);
        $setting = (new Prj1001c())->getSetVals('index','Jessica',true);
        $this->assign('setting',$setting);
//        debugOut($this->getUserInfo());
        return $this->fetch();
    }
    // 捐助
    public function donate(){
        return $this->fetch();
    }
}
