<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/22 0022 8:17
 * Email: brximl@163.com
 * Name:
 */

namespace app\api\controller;


use app\common\controller\Api;
use think\Db;
use think\Request;
use app\common\SCache;

class Article extends Api
{
    /**
     * 文章保存
     */
    public function save(){
        if($this->checkAuth(null,true)) return $this->FeekMsg('非法请求地址!');
        list($data,$mode,$map) = $this->_getSaveData();
        //debugOut([$data,$mode,$map]);return $this->FeekMsg('测试请求!!');
        //数据后台验证
        Db::startTrans();
        $json = null;
        try{
            if($mode == 'A'){
                $uid = getUserInfo('uid');
                if(empty($uid)) return $this->FeekMsg('您还没登录系统!');
                $data['uid'] = $uid;
                //debugOut($data);
                if(Db::table('atc1000c')->insert($data))
                    $json = $this->FeekMsg('数据新增成功',1);
                else
                    $json = $this->FeekMsg('数据新增失败');
            }
            elseif ($mode == 'M'){
                if(Db::table('atc1000c')->where($map)->update($data))
                    $json = $this->FeekMsg('数据更新成功',1);
                else
                    $json = $this->FeekMsg('数据更新失败');
            }
            elseif ($mode == 'D'){
                $this->pushRptBack('atc1000c',$map,'auto');
                $json = $this->FeekMsg('数据删除成功',1);
            }
            Db::commit();
        }catch (\Exception $e){
            Db::rollback();
            debugOut($e->getMessage()."\r\n".$e->getTraceAsString());
        }
        return $json? $json:$this->FeekMsg('数据提交失败!');
    }

    /**
     * 喜欢文章
     * @param  uid
     */
    public function star(){
        $uid = request()->param('uid');
        if($uid){
            // 阅读数处理，不重复保存数据
            $scache = new SCache();
            $count = Db::table('atc1000c')->where('listid',$uid)->value('star_count');
            if($scache->has('index_art1000c_star_ctt',$uid) == false){
                $count = intval($count) + 1;
                Db::table('atc1000c')->where('listid',$uid)->update(['star_count'=>$count]);
                $scache->set('index_art1000c_star_ctt',$uid);
                return $this->FeekMsg(['count'=>$count]);
            }
            return $this->FeekMsg('请勿重复操作!');
        }
        return $this->FeekMsg('数据请求参数无效!');
    }

    /**
     * 评论保存
     */
    public function comment_save(){
        list($data,$mode,$map) = $this->_getSaveData();
        //debugOut([$data,$mode,$map]);
        if($data){
            if($mode == 'A'){
                $uid = getUserInfo('uid');
                if($uid) $data['uid'] = $data;
                $data['ip'] = request()->ip();
                if(Db::table('atc1002c')->insert($data)) return $this->FeekMsg('数据保存成功!',1);
                return $this->FeekMsg('数据保存失败!');
            }elseif ($mode == 'M'){
                if(Db::table('atc1002c')->where($map)->update($data)) return $this->FeekMsg('数据修改成功!',1);
                return $this->FeekMsg('数据修改失败!');
            }elseif ($mode == 'D'){
                $this->pushRptBack('atc1002c',$map,'auto');
                return $this->FeekMsg('数据删除成功!',1);
            }
        }
        return $this->FeekMsg('数据请求参数无效!');
    }
}