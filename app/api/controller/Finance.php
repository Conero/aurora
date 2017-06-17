<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/6/17 0017 20:48
 * Email: brximl@163.com
 * Name: 财务系统公告API
 */

namespace app\api\controller;


use app\common\controller\Api;
use app\common\model\Fnc0010c;

class Finance extends Api
{
    /**
     * 标签保存
     */
    public function tag_save(){
        $uid = getUserInfo('uid');
        if($uid){
            list($data,$mode,$map) = $this->_getSaveData();
            //debugOut([$data,$mode,$map]);
            if($mode == 'A'){
                if(empty($data['tag'])) return $this->FeekMsg('请求参数不完整!');
                $data['uid'] = $uid;
                $fnc = new Fnc0010c($data);
                // 数据重复性检测-> 标签不可重复(根据标签名称/隐私标识判断)
                if($data['private_mk'] == 'Y'){
                    if($fnc->where('tag',$data['tag'])->count()) return $this->FeekMsg('【'.$data['tag'].'】已经存在，无需重复添加或者转为私有!');
                }else{
                    if($fnc->where([
                        'tag' => $data['tag'],
                        'private_mk'    => 'N',
                        'uid'            => $uid
                    ])->count()) return $this->FeekMsg('您私有【'.$data['tag'].'】标签已经存在，无需重复添加!');
                }
                if($fnc->save()) return $this->FeekMsg('标签添加成功!',1);
                return $this->FeekMsg('标签添加失败!');
            }elseif ($mode == 'M'){
                $fnc = new Fnc0010c();
                if($fnc->save($data,$map)) return $this->FeekMsg('标签更新成功!',1);
                return $this->FeekMsg('标签更新失败');
            }elseif ($mode == 'D'){
                if($this->pushRptBack('fnc0010c',$map,'auto')) return $this->FeekMsg('标签删除成功!',1);
                return $this->FeekMsg('标签无法移除!');
            }
        }
        return $this->FeekMsg('请求参数无效!');
    }
}