<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/22 0022 8:17
 * Email: brximl@163.com
 * Name:
 */

namespace app\api\controller;


use app\common\controller\Api;
use think\Db;
use think\Request;

class Article extends Api
{
    /**
     * 文章保存
     */
    public function save(){
        if($this->checkAuth(null,true)) return $this->FeekMsg('非法请求地址!');
        list($data,$mode,$map) = $this->_getSaveData();
        //debugOut([$data,$mode,$map]);return $this->FeekMsg('测试请求!!');
        //数据后台验证
        Db::startTrans();
        $json = null;
        try{
            if($mode == 'A'){
                $uid = getUserInfo('uid');
                if(empty($uid)) return $this->FeekMsg('您还没登录系统!');
                $data['uid'] = $uid;
                //debugOut($data);
                if(Db::table('atc1000c')->insert($data))
                    $json = $this->FeekMsg('数据新增成功',1);
                else
                    $json = $this->FeekMsg('数据新增失败');
            }
            elseif ($mode == 'M'){
                if(Db::table('atc1000c')->where($map)->update($data))
                    $json = $this->FeekMsg('数据更新成功',1);
                else
                    $json = $this->FeekMsg('数据更新失败');
            }
            elseif ($mode == 'D'){
                $this->pushRptBack('atc1000c',$map,'auto');
                $json = $this->FeekMsg('数据删除成功',1);
            }
            Db::commit();
        }catch (\Exception $e){
            Db::rollback();
            debugOut($e->getMessage()."\r\n".$e->getTraceAsString());
        }
        return $json? $json:$this->FeekMsg('数据提交失败!');
    }
}