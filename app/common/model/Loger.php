<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/11 0011 11:10
 * Email: brximl@163.com
 * Name: 系统日志
 */

namespace app\common\model;


use think\Db;
use think\Model;

class Loger extends Model
{
    protected $pk = 'listid';
    private $_childTable = 'sys_logmsg';
    /**
     * @param $code 日志代码
     * @param $content 日志内容
     * @param $delimiter 间隔符号
     * @return bool
     */
    public function write($code,$content,$delimiter=null){
        $delimiter = $delimiter? $delimiter: "\r\n"; // 间隔符
        $data = $this->db()->field('type,listid')->where('code',$code)->find();
        $uid = getUserInfo('uid');
        $success = false;
        if($data){
            $subData = [];
            $type = $data['type']; // Y/M/D/S
            if($type == 'Y' || $type == 'M' || $type == 'D'){ // 年度月度日
                $subData['pid'] = $data['listid'];
                $subData['name'] = ($type == 'Y')? date('Y'):($type == 'M'? date('Ym'):date('Ymd'));
                $subQdt = Db::table($this->_childTable)->field('listid,content')->where($subData)->find();
                if(empty($subQdt)){ // 自动新增
                    $subData['listid'] = getPkValue('pk_sys_logmsg__listid');
                    $subData['content'] = $content;
                    $subData['is_lmsg'] = 'Y';
                    if($uid) $subData['uid'] = $uid;
                    $success = (Db::table($this->_childTable)->insert($subData))? true: false;
                }else{
                    $listid = $subQdt['listid'];
                    $content = $subData['content'].$delimiter.$content;
                    $success = (Db::table($this->_childTable)
                        ->where('listid',$listid)
                        ->update([
                            'content'=>$content,
                            'mtime' => sysdate()
                        ])
                    )? true:false;
                }
            }
            else{
                $subData['pid'] = $data['listid'];
                $subData['name'] = time();
                $subData['listid'] = getPkValue('pk_sys_logmsg__listid');
                $content['msg'] = $content;
                if($uid) $subData['uid'] = $uid;
                $success = (Db::table($this->_childTable)->insert($subData))? true: false;
            }
        }
        return $success;
    }
}