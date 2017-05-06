<?php
/**
 * User: Joshua Conero
 * Date: 2017/5/6 0006 12:13
 * Email: brximl@163.com
 * Name: 移动端首页
 */

namespace app\wap\controller;
use app\common\controller\Wap;
class Index extends Wap
{
    public function index()
    {
        /*
        return '<style type="text/css">*{ padding: 0; margin: 0; } .think_default_text{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
        */
        /*
        return '
            <!doctype html>
            <head>
                <meta charset="utf-8">
                <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0,  user-scalable=0" name="viewport" />
                <title>Aurora - Joshua Coneoro</title>
                <link rel="shortcut icon" href="/aurora/public/aurora-appm.ico"/>
                <link rel="shortcut icon" href="/aurora/public/aurora-appm.ico"/>


                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
                <title>WeUI</title>
                <link rel="stylesheet" href="../style/weui.css"/>
                <link rel="stylesheet" href="./example.css"/>
            </head>
            <body>
                <div style="margin-left:5%;margin-right:5%;">
                    <h4>Aurora 个人应用！ </h4>
                    <p>Author: Joshua Coenro</p>
                    <p>Date: 2017年5月5日 星期五</p>
                    <p>Todate: '.date('Y-m-d H:i:s').'</p>
                </div>
            </body>
            </html>
        ';
        */
        return $this->fetch();
    }
}
