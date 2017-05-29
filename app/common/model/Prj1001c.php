<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/28 0028 9:25
 * Email: brximl@163.com
 * Name: 项目配置项
 */

namespace app\common\model;


use think\Model;

class Prj1001c extends Model
{
    protected $table = 'prj1001c';
    protected $pk = 'listid';

    /**
     * 获取配置项的值/单值
     * @param $key string => groupid.setting_key/setting_key
     * @param null $pid string => pid/code
     * @param bool $isPidCode => $pid->code
     * @return string
     */
    public function getSetVal($key,$pid=null,$isPidCode=false){
        $where = [];
        if(substr_count($key,'.')>0){ // 键值解析 key1.key2 =
            $idx = strpos($key,'.');
            $groupid = substr($key,0,$idx);
            $where['groupid'] = $groupid;
            $where['setting_key'] = substr($key,$idx+1);
        }else $where['setting_key'] = $key;
        if($pid){
            $where['pid'] = $isPidCode? ((new Prj1000c())->where('code',$pid)->value('listid')):$pid;
        }
        $text = '';
        $data = $this->db()->where($where)->find()->toArray();
        if(!empty($data['short_text'])) $text = $data['short_text'];
        elseif(!empty($data['long_text'])) $text = $data['long_text'];
        elseif(!empty($data['json_text'])) $text = $data['json_text'];
        return $text;
    }

    /**
     * 获取分组下所有配置项
     * @param $groupid 分组码
     * @param null $pid
     * @param bool $isPidCode
     * @return array
     */
    public function getSetVals($groupid,$pid=null,$isPidCode=false){
        $retVal = [];
        $where = ['groupid'=>$groupid];
        if($pid){
            $where['pid'] = $isPidCode? ((new Prj1000c())->where('code',$pid)->value('listid')):$pid;
        }
        $data = $this->db()
            ->field('setting_key,short_text,long_text,json_text')
            ->where($where)
            ->order('setting_key')
            ->select();
        foreach ($data as $v){
            $key = $v['setting_key'];
            $text = '';
            if(!empty($v['short_text'])) $text = $v['short_text'];
            elseif(!empty($v['long_text'])) $text = $v['long_text'];
            elseif(!empty($v['json_text'])) $text = $v['json_text'];
            $retVal[$key] = $text;
        }
        return $retVal;
    }
}