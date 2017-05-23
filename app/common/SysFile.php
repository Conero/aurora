<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/23 0023 22:50
 * Email: brximl@163.com
 * Name: 系统文件上床处理
 */

namespace app\common;


use Exception;
use think\Db;
use Dflydev\ApacheMimeTypes\PhpRepository;

class SysFile
{
    const basedir = ROOT_PATH.'/source/';   // 文件所在的文件
    const id_mk = 'pk_sys_file__listid';
    protected $file;    // 系统接受的文件名
    public function __construct($file=null)
    {
        $this->file = $file? $file:$_FILES;
        if(!is_dir(self::basedir)) mkdir(self::basedir);
    }
    // 文件保存
    public function save(){}
    // 文件移除
    public function remove(){}
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
        $default = '.html';
        try{
            // 将网络中的文件保存到服务器端
            $basedir = self::basedir.date('Ym').'/';
            if(!is_dir($basedir)) mkdir($basedir);
            // 获取文件格式名称
            $tArr = explode('/',$url);
            $urlFileName = array_pop($tArr);
            $ext = strrchr($urlFileName,'.');
            if(empty($filename)){
                $filename = $urlFileName;
                $name = $urlFileName;
                $pref = '_'.time();
                if($ext){
                    $filename = str_replace($ext,$pref.$ext,$filename);
                }
                else $filename = $filename.$pref.$default;
            }
            else{
                $filename = ($ext && substr_count($filename,$ext) == 0)? $filename.$ext:$filename;
                $name = $filename; // 自定义时覆盖名称
            }
            $filename = $basedir.$filename;
            ob_start();
            readfile($url);
            $file = ob_get_contents();
            ob_end_clean();
            //文件大小
            $fp2=@fopen($filename,'w');
            @fwrite($fp2,$file);
            @fclose($fp2);

            //  文件写入数据库处理
            $size = $this->sizeUnit(filesize($filename));
            $listid = getPkValue(self::id_mk);
            $saveData = [
                'name' => $name,
                'path'  => date('Ym').'/'.sha1($listid).$ext
            ];
            if($ext){
                $parser = new PhpRepository();
                $mimetype = $parser->findType(str_replace('.','',$ext));
                if($mimetype) $saveData['filetype'] = $mimetype;
            }
            return $this->saveData($saveData);
        }catch(Exception $e){
            $br = "\r\n";
            $rpt = '>> 从URL连接上传文件时出错！'
                . $br .'>> 用户('.($this->nick).')'
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
            $oData = $this->uploadPlusData();
            if(is_array($oData)) $data = array_merge($oData,$data);
            if(isset($data['file_own']) && empty($data['file_own'])) $data['file_own'] = $this->nick;
            $checked = Db::table('sys_file')->insertGetId($data);
            if($checked){
                $this->fileid = $checked;
                // 文件转移
                $modeFileRight = false;
                if(!is_dir($this->curdir)) mkdir($this->curdir);
                if($this->_isUploadMethod == true) $modeFileRight = move_uploaded_file($this->_sourcePath,$this->basedir.$data['url_name']);
                else $modeFileRight = copy($this->_sourcePath,$this->basedir.$data['url_name']);
                // 如果文件上传失败，则删除当前已新增的数据库记录
                if($modeFileRight == false){
                    $map = ['file_id'=>$checked];
                    Db::table('sys_file')->where($map)->delete();
                    return false;
                }
                return true;
            }
        }catch(Exception $e){
            if($checked && !is_bool($checked)){
                try{
                    $map = ['file_id'=>$checked];
                    Db::table('sys_file')->where($map)->delete();
                }catch(Exception $e2){}

                $br = "\r\n";
                $rpt = '>> 新增上传时，保存数据/转移文件时出错！'
                    . $br .'>> 时间('.(date('Y-m-d H:i:s')).')'
                    . $br .'>> 错误信息： '.$br.$e->getTraceAsString();
                ;
                $this->infoRpt($rpt);
            }
        }
        return $checked;
    }
    // 报告方式
    private function infoRpt($info=null){
        if($info) debugOut($info);
    }
}