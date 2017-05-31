<?php

/**
 * Auther: Joshua Conero
 * Date: 2017/5/9 0009 21:28
 * Email: brximl@163.com
 * Name: 微信服务实现
 */
use app\common\model\Loger;
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
            . json_encode(request()->param())
        ;
        $log->write($this->LogCode,$content);
        $this->responseText('欢迎关注,生成测试时');
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
            . json_encode(request()->param())
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
        $log->write($this->LogCode,$content);
        $cmdList = [
            'wz:' => '搜索文章列表'
        ];
        foreach ($cmdList as $k=>$v){
            if(substr_count($text,$k)>0){
                $msg = '';
                switch ($k){
                    case 'wz:':
                        $where = [];
                        $childList = ['a','wj'];
                        foreach ($childList as $vv){
                            if(substr_count($text,$k.$vv)>0){
                                $value = trim(str_replace($k.$vv,'',$text));
                                switch ($vv){
                                    case 'a':
                                        $where = ['sign'=>['like',"%$value%"]];
                                        break;
                                    case 'wj':
                                        $where = ['collected'=>['like',"%$value%"]];
                                        break;
                                }
                                break;
                            }
                        }
                        if(empty($where)) {
                            $value = trim(str_replace($k,'',$text));
                            $where = ['title' => ['like',"%$value%"]];
                        };
                        $data = db()->table('atc1000c')
                            ->field('listid,title,sign,collected,date')
                            ->where($where)
                            ->limit(30)
                            ->select()
                        ;
                        foreach ($data as $dt){
                            $msg .= "\r\n".'<a href="'.(\think\Config::get('setting.p_baseurl')).'wap/article/read/item/'.$dt['listid'].'.html">'.$dt['title'].'('.$dt['collected'].').'.$dt['sign'].' - '.$dt['date'].'</a>
                            ';
                        }
                        $msg = $msg? $msg:'没有找到资源，sorry，guys!';
                        break;
                }
                if($msg){
                    $this->responseText($msg);
                    return;
                }
                break;
            }
        }
        // 输入文章文本
        $this->responseText(
            (new Prj1001c())->getSetVal('weixin_api.cmd_list_help','Jessica',true)
        );
        return;
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
            . json_encode(request()->param())
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
            . json_encode(request()->param())
        ;
        $log->write($this->LogCode,$content);
        $this->responseText('你点击了菜单：' . $this->getRequest('EventKey'));
    }
}