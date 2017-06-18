<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/6/10 0010 21:44
 * Email: brximl@163.com
 * Name:
 */

namespace app\api\controller;


use app\common\Aurora;
use app\common\controller\Api;
use think\Request;
use app\common\model\User as UserModel;

class User extends Api
{
    /**
     * 字符数据字段是否存在
     * request => col,val *
     * @return \think\response\Json
     */
    public function col_exist(){
        $request = Request::instance();
        $col = $request->param('col');
        $value = $request->param('val');
        if($col && $value){
            $user = new UserModel();
            $count = $user->where($col,$value)
                ->count();
            return $this->FeekMsg([
                'exist' => $count>0? 1:-1,
                'msg'   => '数据请求有效'
            ]);
        }
        return $this->FeekMsg('请求参数无效');
    }
    /**
     * 数据保存
     */
    public function save(){
        $useCheck = $this->needLoginNet($uid);
        if($useCheck) return $useCheck;
        $data = request()->param();
        //debugOut([$data,$uid]);
        $model = new UserModel();
        if($model->save($data,['uid'=>$uid])) return $this->FeekMsg('用户信息更新成功！',1);
        return $this->FeekMsg('用户信息更新失败！');
    }
    // 密码修改
    public function passw(){
        $uid = getUserInfo('uid');
        if(empty($uid)) return $this->FeekMsg('非法请求，参数无效!');
        $oldPassw = request()->param('opassw');
        $passw = request()->param('npassw');
        $passw2 = request()->param('npassw_ck');
        if($passw != $passw2) return $this->FeekMsg('密码前后不一致！');
        $model = new UserModel();
        if(!Aurora::checkUserPassw($oldPassw,$model->where('uid',$uid)->value('certificate'))) return $this->FeekMsg('原密码无效，密码更新失败!');
        else{
            if($model->save([
                'certificate' => Aurora::checkUserPassw($passw)
            ],['uid'=>$uid])) return $this->FeekMsg('密码更新成功!',1);
            return $this->FeekMsg('密码更新失败!');
        }
    }
}