<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/23 0023 17:45
 * Email: brximl@163.com
 * Name: 开源中国api测试
 */

namespace app\test\controller;
use hyang\Oschina as Osc;

class Oschina
{
    public function index(){
        $osc = new Osc([
            'client_id' => '2c8a311e68c487e8e815b794f03706f5cf6d38660f27d2ec26ab0603007ac7cc'
            ,'access_token' => '23aa9134ee2fa560529428088e614c5a'
            ,'owner'    => 'Doee'
        ]);
        //println($osc->getAccessToken());
        //return json($osc->getUser());
        return json([$osc->getDeveloper('aurora'),$osc->getDeveloper('Doeeking_V2')]);
        //return json([$osc->getProject()]);
        //return json([$osc->getFollows()]);
    }
}