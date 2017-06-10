<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/6/10 0010 21:44
 * Email: brximl@163.com
 * Name:
 */

namespace app\api\controller;


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
        list($data,$mode,$map) = $this->_getSaveData();
        debugOut([$data,$mode,$map]);
    }
}