<?php

/**
 * Auther: Joshua Conero
 * Date: 2017/5/9 0009 16:22
 * Email: brximl@163.com
 * Name: 微信相关测试
 */
namespace app\test\controller;

header('Content-Type: text/html; charset=utf-8');
// 导入微信开发包
import('wechat-php-sdk.src.Wechat',EXTEND_PATH);
use app\common\wxin\AutoAnswer;
use hyang\Util;
use Wechat;
use hyang\Net;
class Wx
{
    // Coenro - 服务号
//    const secret = '30c44723f7a4a4c7c322f086ec5297a7';
    const aeskey = 'UetAQqUNXwYNv3DXoUtLx6MO4NKbCBs4cmkn9ULJkGY';
//    const appid = 'wx887481468b0439d5';

    // 测试号管理
    const secret = '831746bbd2b6cbe246b2e14630a7a42d';
    const appid = 'wx651d9dea8d5e29dc';

    const token = 'weixin';
    const expire = 7200;    // 默认时长
    private $access_token;
    private $expire_in;
    // 7200s 内 => 2h
    public function index()
    {
        $wechat = new Wechat(array(
            'token' => self::token,
            'aeskey' => self::aeskey,
            'appid' => self::appid,
            'debug' => true
        ));
        $wechat->run();
        return '移动端测试界面';
    }
    public function test(){
//        println(\hyang\Util::RequestData());
//        return '';
//        json()
        /*
        $time = time();
        sleep(6);
        println($time,time(),time()-$time);
        */
        println($this->getAccessToken(),$this->getWeiXinParam('FS'));
        return json(\hyang\Util::RequestData());
    }
    // 通过时间判断
    public function getAccessToken(){
        // 有效的数据重复获取
        if($this->access_token && (time()-$this->expire_in) < self::expire) return $this->access_token;
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.self::appid.'&secret='.self::secret;
        $data = json_decode(Net::get(Net::setUrl($url)),true);
        $this->access_token = $data['access_token'];
        $this->expire_in = time();
        return $this->access_token;
    }
    public function token(){
//        println(request()->param());
//        $type = request()->param('type');
//        return \hyang\Util::RequestData();
        $type = \hyang\Util::RequestData('type');
        if($type) $type = strtoupper($type);
        $data = $this->getWeiXinParam($type);
        return is_array($data)? json($data):$data;
    }

    /**
     * 获取微信参数
     * @param $type
     */
    protected function getWeiXinParam($type){
        switch ($type){
            // 测试成功
            case 'AT': // access_token
                $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.self::appid.'&secret='.self::secret;
                Net::setUrl($url);
                return Net::get();
                break;
            case 'WXIP': // 获取微信服务器IP
                $url = 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token='.json_decode($this->getWeiXinParam('AT'),true)['access_token'];
                return Net::get(Net::setUrl($url));
                break;
            case 'FS':  // 关注用户列表
                $url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$this->getAccessToken();
                return Net::get(Net::setUrl($url));
                break;
            default:
                return ['code'=>-1,'msg'=>'无效'];
        }
    }

    /**
     * 名称发送
     */
    public function cmd(){
        $text = request()->param('text');
        if(request()->has('text')){
            $aa = new AutoAnswer();
            echo nl2br($aa->run($text));
        }

        echo '
            <br>
            <br>
            <br>
            <form action="" method="get">
                <input type="text" name="text">
                <button type="submit">发送</button>
            </form>
        ';
    }
}