<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/9 0009 21:29
 * Email: brximl@163.com
 * Name: 公共接口公共函数
 */

namespace app\common\controller;
use app\common\model\Token;
use app\common\SCache;
use app\common\traits\DbUtil;
use app\common\traits\Util;
use think\Config;
use think\Controller;
use think\Request;
use think\Session;

class Api extends Controller
{
    use DbUtil; // 数据库助手
    use Util;   // 公共方法， Web, Api, Wap
    protected function _initialize(){
        $this->api_check();
        $this->autoRecordApis();
        $this->init();
    }
    protected function init(){}    // 应用初始化接口
    /**
     * 信息反馈
     * @param $msg
     * @param null $code 默认为 失败
     * @return \think\response\Json
     */
    protected function FeekMsg($msg,$code=null){
        $data = [
            'code'=>($code? $code: -1),
            'msg' => $msg
        ];
        return json($data);
    }

    /**
     * 自动登记或同级api请求数据
     */
    protected function autoRecordApis(){
        $request = Request::instance();
        $module = $request->module();
        $contrl = $request->controller();
        $action = $request->action();
        $url =strtolower(Config::get('setting.url_pref').$module."/$contrl/$action");
        $api = model('Apis');
        $data = $api->where('url',$url)->field('listid,count')->find();
        if($data){
            $listId = $data['listid'];
            $count = intval($data['count']) + 1;
            $api->update(['count'=>$count],['listid'=>$listId]);
        }else{
            $uid = getUserInfo('uid');
            $saveData = [
                'url' => $url,
                'count'=>1,
                'module'=>$module,
                'controller'=>$contrl,
                'action'=>$action
            ];
            if($uid) $saveData['uid'] = $uid;
            $api->insert($saveData);
        }
    }

    /**
     * api 请求验证
     */
    protected function api_check(){
        $scache = new SCache();
        $key = Config::get('setting.sckey_name');
        $token = new Token();
        $vtoken = request()->param('token');
        $tokenAble = ($vtoken && $token->TokenIsValid($vtoken))? true:false;
        if(!$scache->has($key,'Y') && !$tokenAble){
            header('content-type:application/json;charset=utf-8;');
            die(json_encode([
                "code"=>-1,
                "msg"=>"非法请求地址"
            ]));
        }
        /*
        $skey = Config::get('setting.session_api_sfkey');
        $svalue = Session::get($skey);
        if(empty($svalue)){
            Session::set($skey,sha1(request()->ip()));
            $svalue = Session::get($skey);
        }
        if(empty($svalue)){
            header('content-type:application/json;charset=utf-8;');
            die(json_encode([
                "code"=>-1,
                "msg"=>"非法请求地址"
            ]));
        }
        */
    }
}