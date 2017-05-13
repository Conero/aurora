<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/10 0010 22:46
 * Email: brximl@163.com
 * Name: 访问你统计
 */

namespace app\common\model;


use think\Db;
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
        $dcct = $this->db()->where('date_format(mtime,\'%Y-%m-%d\')=\''.date('Y-m-d').'\'')->count();
        return [
            'count' => $ctt,    // 总统计数
            'mcount'=> $mctt,   // 手机统计数
            'dcount'=> $dcct,      // 当前统计数
            'wcount'=> $wctt    // 桌面浏览器统计数
        ];
    }

    /**
     * 获取最近的统计量
     * @return array
     */
    public function getVisitCountData(){
        // 全部统计量
        $sql = 'select count(*) as `ctt`,date_format(mtime,\'%Y-%m-%d\') as `date` from sys_visit group by date_format(mtime,\'%Y-%m-%d\') order by date_format(mtime,\'%Y-%m-%d\') asc';
        $data = Db::query($sql);
        // 手机端访问量
        $sql2 = 'select count(*) as `ctt`,date_format(mtime,\'%Y-%m-%d\') as `date` from sys_visit where is_mobile=\'Y\' group by date_format(mtime,\'%Y-%m-%d\') order by date_format(mtime,\'%Y-%m-%d\') asc';
        $data2 = Db::query($sql2);
        $xAxis = [];$series = [];$tmpDt = [];
        foreach ($data as $v){
            $xAxis[] = $v['date'];
            $tmpDt[] = $v['ctt'];
        }
        $series[] = $tmpDt;$tmpDt = [];
        foreach ($data2 as $v){
            $tmpDt[] = $v['ctt'];
        }
        $series[] = $tmpDt;
        $retVal = ['xAxis'=>$xAxis,'series'=>$series];
        return $retVal;
    }
}