<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/10 0010 15:03
 * Email: brximl@163.com
 * Name: 系统令牌数据模型
 */

namespace app\common\model;
use think\Model;

class Token extends Model
{
    protected $pk = 'listid';
    /**
     * 获取权限
     * @param $data  [type*,uid]  类型必填
     * @return string
     */
    public function access_token($data){
        $code = $data['type'];
        if(isset($data['uid'])){
            $code .= (isset($data['uid'])? $data['uid']:'').rand(100, 999).time();
        }else {
            $code .= rand(100000, 99999) . time();
        }
        $token = sha1($code);
        try {
            $data['listid'] = getPkValue('pk_sys_token__listid');
            $data['token'] = $token;
            $this->db()->insert($data);
        }catch (\Exception $e){ // 失败时制空
            $token = '';
        }
        return $token;
    }
    /**
     * 令牌验证是否正确,并且保存访问量
     * @param $token
     * @return bool
     */
    public function TokenIsValid($token){
        $data = $this->db()->where('token',$token)->field('invalid_mk,expire_in,uid,mtime,listid,v_ctt')->find();
        $exist = false;
        if($data && $data['invalid_mk'] != 'Y'){
            $updateCttFn = function () use($data){
                // 更新请求统计次数
                $this->db()->where('listid',$data['listid'])->update(['v_ctt'=>(intval($data['v_ctt'])+1)]);
            };
            if(!empty($data['expire_in'])){
                if(time()-strtotime($data['mtime']) <= $data['expire_in']){
                    $exist = true;
                    call_user_func($updateCttFn);
                }
                else{
                    // 无效以后记录数据库
                    $this->db()->where('listid',$data['listid'])->update(['invalid_mk'=>'Y']);
                }
            }else{
                $exist = true;
                call_user_func($updateCttFn);
            }
        }
        return $exist;
    }
}