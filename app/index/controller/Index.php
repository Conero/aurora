<?php
/**
 * Auther: Joshua Conero
 * Email: brximl@163.com
 * Name: web 端网站首页
 */
namespace app\index\controller;
use app\common\controller\Web;

class Index extends Web
{
    public function index()
    {
        /*
        return '
            <!doctype html>
            <head>
                <title>Aurora - Joshua Coneoro</title>
                <link rel="shortcut icon" href="/aurora/public/aurora.ico"/>
                <link rel="shortcut icon" href="/aurora/public/aurora.ico"/>
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
