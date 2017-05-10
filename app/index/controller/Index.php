<?php
/**
 * Auther: Joshua Conero
 * Email: brximl@163.com
 * Name: web 端网站首页
 */
namespace app\index\controller;
use app\common\controller\Web;
use app\common\model\Visit;

class Index extends Web
{
    public function index()
    {
        $page = [];
        $this->autoRecordVisitRecord();
        // 访问分布
        $rdata = (new Visit())->getVisitCount();
        $page['rate_wctt'] = ceil(($rdata['wcount']/$rdata['count'])*100);
        $page['rate_mctt'] = ceil(($rdata['mcount']/$rdata['count'])*100);
        // 全部统计量
        $page['count'] = $rdata['count'];

        $this->assign('page',$page);
        return $this->fetch();
    }
}
