<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/10 0010 16:47
 * Email: brximl@163.com
 * Name:
 */

namespace app\common\traits;

trait Util
{
    protected $saveConfig = [];
    /**
     * 错误自动重定位
     * @param null $msg
     * @param bool $feekData
     * @return mixed|null|string|void
     */
    protected function getErrorUrl($msg=null,$feekData=false){
        $msg = $msg? $msg:'';
        $url = (IS_MOBILE == 'Y')? urlBuild('!wap:error',"?msg=$msg"):urlBuild('!index:error',"?msg=$msg");
        if($feekData) return $url;
        header('Location: '.$url);
        die('程序执行异常!');
    }
    protected function getHomeUrl($feekData=false){
        $url = (IS_MOBILE == 'Y')? urlBuild('!wap:'):urlBuild('!index:');
        if($feekData) return $url;
        header('Location: '.$url);
        die('程序执行异常!');
    }
    // 删除空值
    protected function unEmptyArray($data){
        $ret = [];
        foreach($data as $k=>$v){
            if($v){
                if(is_int($k)) $ret[] = $v;
                else $ret[$k] = $v;
            }
        }
        return $ret;
    }
    // 获取保存数据 list($data,$mode,$map) = $this->_getSaveData();
    protected function _getSaveData($pk=null,$data=null)
    {
        $pk = $pk? $pk:'listid';
        if(empty($data)) $data = count($_POST)>0? $_POST:$_GET;
        $mode = isset($data['mode'])? $data['mode']:'';
        if($mode) unset($data['mode']);
        $map = isset($data['map'])? $data['map']:'';
        // 保存方法的配置
        $saveConfig = $this->saveConfig? $this->saveConfig:[];
        if($map) unset($data['map']);
        elseif($mode != 'A' && isset($data['pk'])){ //默认名称
            $pkValue = $data['pk'];
            $map = [$pk => isset($saveConfig['raw_pk'])? $pkValue: base64_decode($pkValue)]; // 对应 Bootstrap::formPkGrid 编码方式对应
            if(empty($mode)) $mode = 'M';
            unset($data['pk']);
        }elseif($mode != 'A' && isset($data[$pk])){
            $pkValue = $data[$pk];
            $map = [$pk => isset($saveConfig['raw_pk'])? $pkValue: base64_decode($pkValue)]; // 对应 Bootstrap::formPkGrid 编码方式对应
            if(empty($mode)) $mode = 'M';
            unset($data[$pk]);
        }elseif(empty($mode)) $mode = 'A';
        return [$data,$mode,$map];
    }
    /**
     * 2017年3月10日 星期五 获取 多列表保存数据 - 支持数据清洗
     * @param array/string  $data 数组/ json 字符串
     * @param mixed  $option  选项 string -> 主键名 ; array -> {pk:主键名, + clear: 数据清洗参数}
     * @return array
     * @example list($data,$type,$map) = $this->_getSaveDlist($data);
     */
    protected function _getSaveDlist($data,$option=null)
    {
        $data = is_array($data)? $data : json_decode($data,true);
        if(is_array($option)) $pk = isset($option['pk'])? $option['pk']:'';
        else $pk = $option? $option:'';
        $type = isset($data['type'])? $data['type']:null;
        if($type) unset($data['type']);
        if($pk && isset($data[$pk])){$map = [];$map[$pk] = $data[$pk];unset($data[$pk]); if(empty($type)) $type = 'M';}
        else $map = null;
        if(empty($type) && $pk) $type = 'A';
        if(is_array($option) && isset($option['clear'])) $data = Helper::dataClear($data,$option['clear']);
        return [$data,$type,$map];
    }
    // 获取ajax数据请求数据
    // $data = {'__:':'bsjson 加密数据','$rd':Math.random()}
    // list($item,$data) = $this->_getAjaxData();
    protected function _getAjaxData($onlyPOST=false){
        if($onlyPOST) $data = $_POST;
        else $data = count($_POST)>0? $_POST:$_GET;
        if(isset($data['__:'])) $data = bsjson($data['__:']);
        if(isset($data['$rd'])) unset($data['$rd']);
        $item = isset($data['item'])? $data['item']:'';
        if($item) unset($data['item']);
        return [$item,$data];
    }

    /**
     * 用户权限检查
     * @param null $auth 默认为登录用户
     * @param null $callback function|bool true 是返回值
     * @return mixed 直接调整扩展| bool
     */
    protected function checkAuth($auth=null,$callback=null){
        // 必须未登录用户
        if(empty($auth) && getUserInfo('uid') == ''){
            if($callback instanceof \Closure) return call_user_func($callback,true);
            elseif ($callback) return true;
            $this->getErrorUrl('您该没有登录或注册，该功能将被限制使用!');
        }
    }
}