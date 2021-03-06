<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/13 0013 8:10
 * Email: brximl@163.com
 * Name:
 */

namespace app\api\controller;


use app\common\Aurora;
use app\common\controller\Api;
use app\common\model\Loger;
use hyang\Location;
use think\Db;
use think\Debug;

class Visit extends Api
{
    /**
     * ip 统计接口： 接受参数： limit 默认 30
     * @return \think\response\Json
     */
    public function getAreaByIp(){
        $limit = request()->param('limit');
        $limit = $limit? $limit:30;
        $visit = model('Visit');
        $data = $visit->where('ip not like \'192.168.%\' and ip <>\'127.0.0.1\'')
            ->where('annlyse_mk <>\'Y\' or annlyse_mk is null')
            ->field('ip,listid')
            ->limit($limit)
            ->select();
        $succssCtt = 0;$errLog = '';$msg='';
        Debug::remark('begin');
        try {
            foreach ($data as $v) {
                //println($v['ip'],Location::getLocation(Location::setIp($v['ip'])));
                // 已经执行，但annlyse_mk值更新失败的记录自动更新
                Db::execute('update `sys_visit` set `annlyse_mk`=\'Y\' where `ans_time` is not null and `province` is not null');
                $ansDt = Location::getLocation(Location::setIp($v['ip']));
                if ($ansDt['code'] == '0') { // 请求正确时
                    $succssCtt += 1;    // 正统统计
                    $ansDt = $ansDt['data'];
                    $visit->save([
                        'province' => $ansDt['region'],
                        'city' => $ansDt['city'],
                        'isp' => $ansDt['isp'],
                        'annlyse_mk' => 'Y',
                        'ans_data' => json_encode($ansDt),
                        'ans_time' => sysdate()
                    ], ['listid' => $v['listid']]);
                } else { // 数据请求失败
                    $visit->save([
                        'annlyse_mk' => 'Y',
                        'ans_data' => '{"remark":"数据请求失败"}',
                        'ans_time' => sysdate()
                    ], ['listid' => $v['listid']]);
                }
            }
        }catch (\Exception $e){
            $errLog = $e->getMessage();
            $msg = '，api请求过程中出现异常';
        }
        Debug::remark('end');
        $logmsg = "ip地址“".(request()->ip())."”为用户请求了该API,执行情况$succssCtt/".count($data);
        if($errLog) $logmsg .= ",错误异常信息：".$errLog;
        $logmsg .= "，运行耗时".Debug::getRangeTime('begin','end').'s';
        $logmsg .= ",运行内存".Debug::getRangeMem('begin','end').'kb';
        (new Loger())->write('ip_ans_area',$logmsg);
        return $this->FeekMsg("服务器已经响应".$msg,1);
    }

    /**
     * 获取到分布式数据，用于echart 构图
     * @return \think\response\Json
     */
    public function getDistributionCtt(){
//        $visit = model('Visit');
//        $data = $visit->where('province is not null')
//            ->field("replace(replace(province,'省',''),'市','') as province,
//    count(*) as ctt")
//            ->select();
        $data = db()->query("SELECT 
	replace(replace(province,'省',''),'市','') as province,
    count(*) as ctt FROM `sys_visit` WHERE province is not null GROUP by province");
        $retVal = [];
        foreach ($data as $v){
            $retVal[] = [
                'name' => $v['province'],
                'value' => $v['ctt'],
            ];
        }
        return json($retVal);
    }
    /**
     * 获取访客的地址信息
     * @return \think\response\Json
     */
    public function location(){
        $data = Aurora::location();
        return json($data);
    }
}