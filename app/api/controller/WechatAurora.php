<?php

/**
 * Auther: Joshua Conero
 * Date: 2017/5/9 0009 21:28
 * Email: brximl@163.com
 * Name: 微信服务实现
 */
use app\common\model\Loger;
use app\common\wxin\AutoAnswer;
use app\common\model\Prj1001c;
import('wechat-php-sdk.src.Wechat',EXTEND_PATH);
class WechatAurora extends Wechat
{
    public $LogCode = 'weixin_dyh_log';
    /**
     * 用户关注时触发，回复「欢迎关注」
     * @return void
     */
    protected function onSubscribe() {
        $log = new Loger();
        $content = request()->ip()
            ."用于关注订阅号，关注时触发接口!"
            ."\r\n请求数据：\r\n"
            .print_r(request()->param(),true)
        ;
        $msg = (new Prj1001c())->getSetVal('weixin_api.when_star_event', 'Jessica', true);
        $log->write($this->LogCode,$content);
        $msg = $msg? $msg:'欢饮您的关注';
        $this->responseText($msg);
    }

    /**
     * 用户取消关注时触发
     *
     * @return void
     */
    protected function onUnsubscribe() {
        $log = new Loger();
        $content = request()->ip()
            .">用户取消关注时触发"
            ."\r\n请求数据：\r\n"
            .print_r(request()->param(),true)
        ;
        $log->write($this->LogCode,$content);
        // 「悄悄的我走了，正如我悄悄的来；我挥一挥衣袖，不带走一片云彩。」
    }

    /**
     * 收到文本消息时触发，回复收到的文本消息内容
     *
     * @return void
     */
    protected function onText() {
        $text = trim($this->getRequest('content'));
        $log = new Loger();
        $content = request()->ip()
            .">收到文本消息时触发，回复收到的文本消息内容"
            ."\r\n文本内容：\r\n"
            .$text
            ."\r\n请求数据：\r\n"
            .print_r(request()->param(),true)
            //. json_encode(request()->param())
        ;
        try {
            $auto = new AutoAnswer();
            $msg = $auto->run($text);
            $content .= "响应文本：\r\n" . $msg;
            if (empty($msg)) {
                $content .= "\r\nError: AutoAnswer 获取文本失败(1)!";
                $msg = (new Prj1001c())->getSetVal('weixin_api.cmd_list_help', 'Jessica', true);
            }
            if (empty($msg)) { // 第二尝试
                $content .= "\r\nError: Prj1001c 获取帮助失败，尝试获取手动文本(2)!";
                $msg = $auto->getCmdDocs();
            }
        }catch (Exception $e){
            if(empty($msg)){
                $content .= "\r\nError: Prj1001c 尝试获取手动文本(3)，文本获取失败了。!\r\n程序错误报告：".$e->getMessage();
                $msg = "\r\n Sorry,guys! I'm so down,now!";
            }
        }

        $log->write($this->LogCode,$content);
        // 输入文章文本
        $this->responseText($msg);
        //$this->responseText('收到了文字消息：' . $this->getRequest('content'));
    }

    /**
     * 收到链接消息时触发，回复收到的链接地址
     *
     * @return void
     */
    protected function onLink() {
        $log = new Loger();
        $content = request()->ip()
            .">收到链接消息时触发，回复收到的链接地址"
            ."\r\n请求数据：\r\n"
            .print_r(request()->param(),true)
        ;
        $log->write($this->LogCode,$content);
        $this->responseText('收到了链接：' . $this->getRequest('url'));
    }

    /**
     * 收到自定义菜单消息时触发，回复菜单的EventKey
     *
     * @return void
     */
    protected function onClick() {
        $log = new Loger();
        $content = request()->ip()
            .">收到自定义菜单消息时触发，回复菜单的EventKey"
            ."\r\n请求数据：\r\n"
            .print_r(request()->param(),true)
        ;
        $log->write($this->LogCode,$content);
        $this->responseText('你点击了菜单：' . $this->getRequest('EventKey'));
    }
}