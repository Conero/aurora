<?php
/**
 * Auther: Joshua Conero
 * Email: brximl@163.com
 * Name: web 端网站首页
 */
namespace app\index\controller;
use app\common\controller\Web;
use app\common\model\Visit;
use think\Config;
use think\Db;

class Index extends Web
{
    public function index()
    {
        $this->loadScript([
            'js' => ['/echart/echarts.min','index/index']
        ]);
        $page = [];
        $this->autoRecordVisitRecord();
        // 访问分布
        $rdata = (new Visit())->getVisitCount();
        $page['rate_wctt'] = ceil(($rdata['wcount']/$rdata['count'])*100);
        $page['rate_mctt'] = ceil(($rdata['mcount']/$rdata['count'])*100);
        // 全部统计量
        $page['count'] = $rdata['count'];
        $page['dcount'] = $rdata['dcount'];
        $oldt = Config::get('setting.online_date');
        $page['online_cttdt'] = getDays(date('Y-m-d'),$oldt);
        $page['online_dt'] = $oldt;

        $this->assign('page',$page);
        // 数据
        $data = [];
        $article = '';
        $qData = Db::table('atc1000c')
            ->order('date desc')
            ->where('is_private','N')
            ->limit(8)
            ->select();
        $count = Db::table('atc1000c')
            ->where('is_private','N')
            ->count();
        foreach ($qData as $v){
            $article .= '<li class="list-group-item d-flex justify-content-between align-items-center">
                <div class="mr-auto p-2">
                    <a href="'.url('essay/read','item='.$v['listid']).'" class="font-weight-bold text-info">'.$v['title'].'</a>
                    <span class="font-italic">('.$v['collected'].')</span>
                </div>
                <div class="p-2">
                    <i class="fa fa-eye text-danger"></i> '.$v['read_count'].'
                    '.(empty($v['star_count'])? '':'<i class="fa fa-star text-success"></i>'.$v['star_count']).'
                    '.$v['date'].'
                 </div>
                </li>';
        }
        $data['article'] = $article;
        $data['article_count'] = $count;
        $this->assign('data',$data);
        return $this->fetch();
    }
}
