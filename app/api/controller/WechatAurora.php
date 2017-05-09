<?php

/**
 * Auther: Joshua Conero
 * Date: 2017/5/9 0009 21:28
 * Email: brximl@163.com
 * Name: 微信服务实现
 */
import('wechat-php-sdk.src.Wechat',EXTEND_PATH);
class WechatAurora extends Wechat
{
    /**
     * 用户关注时触发，回复「欢迎关注」
     *
     * @return void
     */
    protected function onSubscribe() {
        $this->responseText('欢迎关注,生成测试时');
    }

    /**
     * 用户取消关注时触发
     *
     * @return void
     */
    protected function onUnsubscribe() {
        // 「悄悄的我走了，正如我悄悄的来；我挥一挥衣袖，不带走一片云彩。」
    }

    /**
     * 收到文本消息时触发，回复收到的文本消息内容
     *
     * @return void
     */
    protected function onText() {
        $this->responseText('收到了文字消息：' . $this->getRequest('content'));
    }
}