<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/28 0028 9:36
 * Email: brximl@163.com
 * Name: 项目管理相关api
 */

namespace app\api\controller;


use app\common\controller\Api;
use app\common\model\Prj1001c;

class Project extends Api
{
    /**
     * 设置项数据保存
     */
    public function setting_save(){
        list($data,$mode,$map) = $this->_getSaveData();
        //debugOut([$data,$mode,$map]);
        if($mode == 'A'){
            // 新增是检测是否已经存在数据库中 通过 分组码.键名.项目名称
            $prjSet = new Prj1001c();
            $where = ['pid'=>$data['pid'],'setting_key'=>$data['setting_key']];
            if(isset($data['groupid']) && empty($data['groupid'])) $where['groupid'] = $data['groupid'];
            if($prjSet->where($where)->count() > 0) return $this->FeekMsg('数据已经存在，请勿保存重复的数据!');
            // 数据不重复时保存数据
            $data['listid'] = getPkValue('pk_prj1001c__listid');
            $uid = getUserInfo('uid');
            if($uid) $data['uid'] = $uid;
            //$prjSet = new Prj1001c($data);
            $prjSet->data($data);
            if($prjSet->save()) return $this->FeekMsg('配置项保存成功！',1);
            return $this->FeekMsg('配置项保存失败！');
        }elseif ($mode == 'M'){
            $prjSet = new Prj1001c();
            if($prjSet->save($data,$map)) return $this->FeekMsg('配置项更新成功！',1);
            return $this->FeekMsg('配置项更新失败！');
        }elseif ($mode == 'D'){
            $this->pushRptBack('prj1001c',$map,'auto');
            return $this->FeekMsg('配置项删除成功！',1);
        }
        return $this->FeekMsg('数据请求无效！');
    }

    /**
     * 获取数据项信息
     * @param uid
     * @return \think\response\Json
     */
    public function get_setting(){
        $listid = request()->param('uid');
        if($listid){
            $data = Prj1001c::get($listid)->toArray();
            return $this->FeekMsg($data);
        }
        return $this->FeekMsg('数据请求无效');
    }
}