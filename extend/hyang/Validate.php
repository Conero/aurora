<?php
/* 2017年1月17日 星期二
 * 数据验证
 */
// preg_match()
namespace hyang;
class Validate{
    // 是否时ip
    public static function ipv4($ipv4)
    {
        $preg = "/[0-9.]{8,12}/";
        if(preg_match($preg,$ipv4) > 0) return true;
        return false;
    }
    // 是否为合法日期 2017-07-06
    public static function isDate($value){
        $preg = '/([\d]{4})+(-|\/)+([\d]{2})+(-|\/)+([\d]{2})/';
        if(preg_match($preg,$value)) return true;
        return false;
    }
}