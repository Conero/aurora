<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/31 0031 23:42
 * Email: brximl@163.com
 * Name: 微信自动复数，解析发送的文本信息
 */

namespace app\common\wxin;


use think\Config;
use think\Db;
use app\common\model\Prj1001c;

class AutoAnswer
{
    protected $cmd;         // 解析以后的口令
    protected $value;       // 解析以后的值
    protected $CmdClass;
    protected $CmdClassConf = '/AutoAnswerCmdList.json';
    protected $query;       // think/Db 对象 -> where
    protected $LimitList = 12;  // 限制长度
    // 文档
    protected $CmdDocs;
    public function __construct()
    {
        // 获取配置中的命令行
        $this->CmdClassConf = __DIR__.'/'.$this->CmdClassConf;
        $this->CmdClass = is_file($this->CmdClassConf)?
            json_decode(file_get_contents($this->CmdClassConf),true):[];
        /*
        // 默认文档
        $year = date('Y');
        $this->CmdDocs = <<<EOF
        欢迎来到 <a href="http://www.conero.cn/">Coenro.cn</a>, 我是 Aurora Coenro
         *  wz: 按内容/标题               搜索文章
                wz:a        按作者搜索文章
                wz:wj       按文集搜索文章
                
         2014-$year@Conero
         Joshua Conero Doeeking Yang
EOF;
        */
        $this->CmdDocs = (new Prj1001c())->getSetVal('weixin_api.cmd_list_help','Jessica',true);
    }

    /**
     * 文本解析出 命令
     * @param null $text
     */
    protected function parseText($text=null){
        if(empty($text)) return null;
        foreach ($this->CmdClass as $k=>$v){
            $pattern = "/".$k."/i";
            if(preg_match($pattern,$text)>0){
                $this->cmd = $k;
                $text = trim(preg_replace($pattern,'',$text));
                $child = isset($v['child'])? $v['child']:[];
                $where  = [];
                foreach ($child as $cKey=>$filed){
                    $patternChild = "/([:.-])(".$cKey.")/i";
                    //println(preg_match($patternChild,$text),$patternChild,$text,$filed,$cKey);
                    if(preg_match($patternChild,$text)>0){
                        $value = trim(preg_replace($patternChild,'',$text));
                        $where[$filed] = ['like',"%$value%"];
                        //println($where);
                        break;
                    }
                }

                //println($where);
                if(empty($where) && $text){
                    $baseWhere = ['like',"%$text%"];
                    //$where = is_array($v['default'])? array_combine($v['default'],Util::ReplaceArray($baseWhere,count($v['default']))):[$v['default']=>$baseWhere];
                    $where = is_array($v['default'])? [implode('|',$v['default'])=>$baseWhere]:[$v['default']=>$baseWhere];
                }
                // 特殊处理
                switch ($this->cmd){
                    case 'wz':
                        $tmpWhere = ['is_private'=>'N'];
                        $where = $where? array_merge($where,$tmpWhere):$tmpWhere;
                        break;
                }
                $where = $where? $where:null;
                return Db::table($v['table'])
                    ->field($v['filed'])
                    ->where($where)
                    ->limit($this->LimitList)
                    ->select();
            }
        }
    }
    public function run($text=null){
        $str = "";
        $data = $this->parseText($text);
        if($this->cmd && method_exists($this,($this->cmd).'CmdAction')) $str = call_user_func([$this,($this->cmd).'CmdAction'],$data);
        elseif (empty($this->cmd) && $text){ // 系统默认
            // 版本号
            if(preg_match("/(version)|(-v)/i",$text)) $str = "\r\n版本号： ".Config::get('setting.version').'('.Config::get('setting.build').')';
        }
        if(empty($str)) $str = $this->CmdDocs;
        return $str;
    }
    // 文章 - 命令排版处理
    public function wzCmdAction($data=null){
        $str = '没有找到资源，sorry，guys!';
        $data = is_array($data)? $data:[];
        $list = '';
        foreach ($data as $v){
            $list .= "\r\n".'<a href="'.Config::get('setting.p_baseurl').'wap/article/read/item/'.$v['listid'].'.html">《'.$v['title'].'》'.(empty($v['collected'])? '':'('.$v['collected'].')').' - '.$v['sign'].'</a>';
        }
        $str = $list? $list:$str;
        return $str;
    }
}