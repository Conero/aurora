<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/13 0013 17:09
 * Email: brximl@163.com
 * Name: session 缓存机制扩展，用于反正文章阅读数重复统计
 */

namespace app\common;


use hyang\Util;
use think\Config;
use think\Request;
use think\Session;

class SCache
{
    public $sesson_file;
    private $session_fpath;
    private $ip;
    private $basedir;
    private $session_cache = array();
    public function __construct()
    {
        $request = Request::instance();
        $this->ip = $request->ip();
        $sKey = Config::get('setting.session_cache');
        $sValue = Session::get($sKey);
        $this->sesson_file = $sValue;
        if(empty($this->sesson_file)){
            $this->sesson_file = sha1(($this->ip).rand(100000,999999));
            Session::set($sKey,$this->sesson_file);
        }
        $this->basedir = ROOT_PATH.Config::get('setting.session_cache_dir').date('Y').'/'.date('m').'/'.date('d').'/';
        $this->session_fpath = $this->basedir.$this->sesson_file;
    }
    /**
     * 查看键值是否存在
     * @param $key array 一级键值
     * @param $value string 以及值
     * @return bool
     */
    public function has($key,$value){
        $data = $this->session_cache;
        $file = $this->session_fpath;
        if(is_file($file) && empty($data)){
            // 编码方式
            $data = unserialize(base64_decode(file_get_contents($file)));
            $this->session_cache = $data;
        }
        if(!empty($data) && is_array($data)){
            $tmpArray = array_key_exists($key,$data)? $data[$key]:null;
            return $tmpArray? (is_array($tmpArray) && in_array($value,$tmpArray)? true:false):false;
        }
        return false;
    }

    /**
     * 设置值
     * @param $key array 一级键值
     * @param $value string 以及值
     * @return bool|string
     */
    public function set($key,$value){
        $data = $this->session_cache;
        $data = is_array($data)? $data:[];
        $tmpArray = isset($data[$key])? $data[$key]:[];
        if(!in_array($key,$tmpArray)){
            if(is_array($value)){
                if(!empty($tmpArray)){
                    foreach ($value as $v){
                        if(!in_array($v,$tmpArray)) $tmpArray[] = $v;
                    }
                }else $tmpArray = $value;
            }
            else $tmpArray[] = $value;
            $data[$key] = $tmpArray;
            $this->session_cache = $data;
            $content = base64_encode(serialize($data));
            Util::mkdirs($this->basedir);
            return file_put_contents($this->session_fpath,$content);
        }
        return true;
    }

    /**
     * v-k 单值存在性判断
     * @param $key
     * @return bool
     */
    public function key_exist($key){
        $data = $this->session_cache;
        $file = $this->session_fpath;
        if(is_file($file) && empty($data)){
            // 编码方式
            $data = unserialize(base64_decode(file_get_contents($file)));
            $this->session_cache = $data;
        }
        return isset($data[$key]);
    }

    /**
     * k-v 值设置
     * @param $key
     * @param $value
     * @return bool|int
     */
    public function setKv($key,$value){
        if(!$this->key_exist($key)){
            $data = $this->session_cache;
            $data = is_array($data)? $data:[];
            $data[$key] = $value;
            $this->session_cache = $data;
            $content = base64_encode(serialize($data));
            Util::mkdirs($this->basedir);
            return file_put_contents($this->session_fpath,$content);
        }
        return true;
    }
}