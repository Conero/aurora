<?php
/**
 * User: Joshua Conero
 * Date: 2017/5/6 0006 12:13
 * Email: brximl@163.com
 * Name: 移动端首页
 */

namespace app\wap\controller;
use app\common\model\Prj1001c;
use app\common\model\Prj1002c;
use think\Config;
use app\common\controller\Wap;
use app\common\model\Visit;
class Index extends Wap
{
    // 首页
    public function index()
    {
        $this->loadScript([
            'js'    => ['index/index'],
            'css'   => ['index/index']
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
        // 系统通知公告
        $prj2 = new Prj1002c();
        $data = $prj2->getInfos(Config::get('setting.prj_code'),false,function ($query){
            return $query->limit(2)->select();
        });
        //println(Config::get('setting.prj_code'),$data);
        $informList = '';
        foreach ($data as $v){
            $informList .= '
            <a class="weui-cell weui-cell_access" href="'.url('inform/read','item='.$v['listid']).'">
                <div class="weui-cell__hd"></div>
                <div class="weui-cell__bd weui-cell_primary">
                    <p>'.$v['title'].'</p>
                </div>
                <span class="weui-cell__ft"></span>
            </a>
            ';
        }
        if($informList) $page['inform_list'] = $informList;
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
