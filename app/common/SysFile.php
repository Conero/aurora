<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/23 0023 22:50
 * Email: brximl@163.com
 * Name: 系统文件上床处理
 */

namespace app\common;


use app\common\traits\DbUtil;
use Exception;
use think\Db;
use Dflydev\ApacheMimeTypes\PhpRepository;

class SysFile
{
    use DbUtil;
    const id_mk = 'pk_sys_file__listid';
    const dirname = '/source/';
    protected $file;    // 系统接受的文件名
    protected $basepath;
    protected $basedir; // 文件目录
    protected $registerEvents = []; // [key => callback   注册事件
    public function __construct($file=null)
    {
        $this->file = $file? $file:$_FILES;
        $basedir = ROOT_PATH.self::dirname;
        if(!is_dir($basedir)) mkdir($basedir);
        $this->basepath = date('Ym').'/';
        $this->basedir = $basedir.$this->basepath;
    }

    /**
     * 新增前事件处理
     * @param $callback  => function($data,$type=SF|F1){}
     */
    public function beforeInsert($callback){
        if($callback instanceof \Closure) $this->registerEvents['beforeInsert'] = $callback;
    }

    /**
     * 文件保存
     * @param $pid string 在分组中信息或者直接新增住
     * @return bool
     */
    public function save($pid=nulll){
        $success = false;
        $pid = $pid? $pid:null;
        foreach ($this->file as $v){
            // 出错时直接跳过
            if($v['error']) continue;
            $lisid = getPkValue(self::id_mk);
            $path = $this->basepath.$lisid;
            $name = $v['name'];
            $value = [
                'listid' => $lisid,
                'name' => $name,
                'filetype' => $v['type'],
                'size'  => $this->sizeUnit($v['size']),
                'path'  => $path
            ];
            if(empty($pid)){
                $fileGroup = ['mtime'=>date('Y-m-d H:i:s')];
                $uid = getUserInfo('uid');
                if(key_exists('beforeInsert',$this->registerEvents)){
                    $fileGroup = call_user_func($this->registerEvents['beforeInsert'],$fileGroup,'F1');
                }
                if($uid) $fileGroup['uid'] = $uid;
                $pid = Db::table('file1000c')->insertGetId($fileGroup);
            }
            $value['pid'] = $pid;
            if(key_exists('beforeInsert',$this->registerEvents)){
                $value = call_user_func($this->registerEvents['beforeInsert'],$value,'SF');
            }
            if(Db::table('sys_file')->insert($value)) {
                move_uploaded_file($v['tmp_name'], $this->basedir . $name);
                $success = true;
            }
        }
        return $success;
    }
    /**
     * 文件移除
     * @param $pid string|array
     * @return null
     */
    public function remove($pid){
        if(is_array($pid)){
            foreach ($pid as $v){
                $this->remove($v);
            }
            return null;
        }
        $data = Db::table('sys_file')
            ->field('listid,path')
            ->where('pid',$pid)
            ->select();
        foreach ($data as $v){
            $file = ROOT_PATH.self::dirname.$v['path'];
            if(is_file($file)){
                unlink($file);
            }
            $map = ['listid'=> $v['listid']];
            $this->pushRptBack('sys_file',$map,'auto');
        }
        $this->pushRptBack('file1000c',['listid'=>$pid],'auto');
    }
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 2016/1/24 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
    //文件单位化  $fizese 文件原始大小：
    public function sizeUnit($fizese)
    {
        if(!$fizese)
            return false;
        $fizese = intval($fizese);
        $tmpKb = $fizese/1024;
        if($tmpKb < 1){
            $size = $fizese.'Byte(B)';
        }
        else{
            $tmpMb = $tmpKb/1024;
            if($tmpMb < 1){
                $size = round($tmpKb,3).'KB';
            }
            else{
                $tmpGb = $tmpMb/1024;
                if($tmpGb < 1){
                    $size = round($tmpMb,3).'MB';
                }
                else{
                    $tmpTb = $tmpGb/1024;
                    if($tmpTb < 1){
                        $size = round($tmpGb,3).'GB';
                    }
                    else{
                        $size = round($tmpTb,3).'TB';
                    }
                }
            }
        }
        return $size;
    }
    // URL 中加载文件 - 基于 curl
    public function fromUrl($url,$filename=null){
        try{
            // 将网络中的文件保存到服务器端
            $basedir = $this->basedir;
            if(!is_dir($basedir)) mkdir($basedir);
            $listid = getPkValue(self::id_mk);
            // 获取文件格式名称
            $pathinfo = pathinfo($url);
            $ext  = $pathinfo['extension'];
            if(empty($filename)){
                $filename = $pathinfo['basename'];
            }
            else{
                $filename = ($ext && substr_count($filename,$ext) == 0)? $filename.$ext:$filename;
            }
            $name = $filename; // 自定义时覆盖名称
            $filename = $listid.'.'.$ext;

            // 网络文件处理
            ob_start();
            readfile($url);
            $file = ob_get_contents();
            ob_end_clean();
            //文件大小
            $fp2=@fopen($basedir.$filename,'w');
            @fwrite($fp2,$file);
            @fclose($fp2);

            //  文件写入数据库处理
            $size = $this->sizeUnit(filesize($basedir.$filename));
            $saveData = [
                'name' => $name,
                'size'=> $size,
                'path'  => $this->basepath.$filename
            ];
            if($ext){
                $parser = new PhpRepository();
                $mimetype = $parser->findType(str_replace('.','',$ext));
                if($mimetype) $saveData['filetype'] = $mimetype;
            }
            $saveData['listid'] = $listid;
            $feekRet = $this->saveData($saveData);
            return $feekRet;
        }catch(Exception $e){
            $br = "\r\n";
            $rpt = '>> 从URL连接上传文件时出错！'
                . $br .'>> 时间('.(date('Y-m-d H:i:s')).')'
                . $br .'>> 错误信息： '.$br.$e->getTraceAsString();
            ;
            $this->infoRpt($rpt);
            return false;
        }
    }
    // 返回 bool
    /* 上游数组字段:   [user_code,file_name,file_type,file_size,file_no,url_name]
     *
    */
    public function saveData($data){
        $checked = false;
        try{
            $fileGroup = ['mtime'=>date('Y-m-d H:i:s')];
            $uid = getUserInfo('uid');
            if(key_exists('beforeInsert',$this->registerEvents)){
                $fileGroup = call_user_func($this->registerEvents['beforeInsert'],$fileGroup,'F1');
            }
            if($uid) $fileGroup['uid'] = $uid;
            $pid = Db::table('file1000c')->insertGetId($fileGroup);
            //debugOut([$pid,$fileGroup,$data]);
            if($pid){
                $data['pid'] = $pid;
                if(key_exists('beforeInsert',$this->registerEvents)){
                    $data = call_user_func($this->registerEvents['beforeInsert'],$data,'SF');
                }
                Db::table('sys_file')->insert($data);
                $checked = true;
            }
        }catch(Exception $e){
            $br = "\r\n";
            $rpt = '>> 新增上传时，保存数据/转移文件时出错！'
                . $br .'>> 时间('.(date('Y-m-d H:i:s')).')'
                . $br .'>> 错误信息： '.$e->getMessage().$br.$e->getTraceAsString();
            ;
            $this->infoRpt($rpt);
        }
        return $checked;
    }
    // 报告方式
    private function infoRpt($info=null){
        if($info) debugOut($info);
    }
}