<?php
/**
 *============================
 * author:Farmer
 * time:2017/7/26 10:33
 * blog:blog.icodef.com
 * function:
 *============================
 */

namespace app\admin\ctrl;


use app\common\ctrl\auth;

class movie extends auth {
    public function index($page = 1) {
        $total=50;
        V()->assign('title', '影视管理');
        $rec = DB('video as a|user as b')->select(['a.uid=b.uid', 'father_vid' => -1,'__order by'=>'status,vid desc', '__limit' => (($page - 1) * $total) . ','.$total]);
        $movie_list = $rec->fetchAll();
        $count=DB('video')->find(['father_vid' => -1],'count(*)')['count(*)'];
        V()->assign('page',$page);
        V()->assign('pageAll',ceil($count/$total));
        V()->assign('movie_list', $movie_list);
        $online=DB('ip')->find(['ip_time'=>[time()-60,'>'],'type'=>1],'count(*)')['count(*)'];
        V()->assign('online', $online);

        $online=DB('ip')->find(['ip_time'=>[time()-60,'>'],'type'=>1],'count(*)')['count(*)'];
        V()->assign('online', $online);
        $up=DB('ip')->find(['ip_time'=>[strtotime(date('Y/m/d 00:00:00')),'>'],'ip_time<'.strtotime(date('Y/m/d 23:59:59')),'type'=>1],'count(*)')['count(*)'];
        V()->assign('up',$up);
        $yesterday=DB('ip')->find(['ip_time'=>[strtotime(date('Y/m/d 00:00:00'))-86400,'>'],'ip_time<'.(strtotime(date('Y/m/d 23:59:59'))-86400),'type'=>1],'count(*)')['count(*)'];
        V()->assign('yesterday',$yesterday);
        V()->display();
    }
    public function edit($vid = 0, $fvid = 0) {
        V()->assign('title', '影视管理');
        V()->assign('fvid', $fvid ?: $vid);
        $msg = [];
        if (!($msg = DB('video')->find(['vid' => $vid]))) {
            $msg['name'] = '';
            $msg['vid'] = '';
            $msg['pay'] = '';
            $msg['introduction'] = '';
            $msg['url'] = '';
            $msg['image_url'] = '';
            $msg['release_time'] = '';
            $msg['type'] = '';
            $msg['mark'] = '';
            $msg['status'] = 0;
            if ($fvid > 0) {
                $msg['father_vid'] = $fvid;
            } else {
                $msg['father_vid'] = -1;
            }

        }
        $msg['child'] = [];
        if ($rec = DB('video')->select(['father_vid' => $vid])) {
            $msg['child'] = $rec->fetchAll();
        }
        V()->assign('video', $msg);
        V()->display();
    }

    public function post($vid = 0, $fvid = 0) {
        $retJson = ['code' => -1, 'msg' => '错误,vid不存在'];
        if ($fvid > 0 and $vid>0) {//更新子集
            $ret = isExist($_POST, [
                'name' => ['msg' => '请输入影视名字', 'sql' => 'name'],
                'url'=> ['msg' => '请输入下载链接', 'sql' => 'url'],
                'status'=> ['msg' => '请输入集数', 'sql' => 'status']], $sql);
            $sql['pay']=input('post.pay');
            if ($ret === true) {
                $retJson = ['code' => 1, 'msg' => '成功'];
                DB('video')->update($sql,['vid'=>$vid]);
            } else {
                $retJson['msg'] = $ret;
            }
        } else if ($vid > 0) {//更新集
            $ret = isExist($_POST, [
                'name' => ['msg' => '请输入影视名字', 'sql' => 'name'],
                'image_url'=> ['msg' => '请输入图片链接', 'sql' => 'image_url']], $sql);
            $sql['type']=input('post.type');
            $sql['pay']=input('post.pay');
            $sql['mark']=input('post.mark');
            $sql['status']= input('post.status')=='true'?:0;
            $sql['introduction']=input('post.introduction');
            $sql['release_time']=input('post.release_time');
            $sql['url']=input('post.url');
            if ($ret === true) {
                $retJson = ['code' => 1, 'msg' => '成功'];
                DB('video')->update($sql,['vid'=>$vid]);
            } else {
                $retJson['msg'] = $ret;
            }
        }else if($fvid>0){//添加子集
            $ret = isExist($_POST, [
                'name' => ['msg' => '请输入影视名字', 'sql' => 'name'],
                'url'=> ['msg' => '请输入下载链接', 'sql' => 'url'],
                'status'=> ['msg' => '请输入集数', 'sql' => 'status']], $sql);
            $sql['pay']=input('post.pay');
            $sql['father_vid']=$fvid;
            if ($ret === true) {
                $retJson = ['code' => 1, 'msg' => '成功'];
                DB('video')->insert($sql);
            } else {
                $retJson['msg'] = $ret;
            }
        } else {//添加集
            $ret = isExist($_POST, [
                'name' => ['msg' => '请输入影视名字', 'sql' => 'name'],
                'image_url'=> ['msg' => '请输入图片链接', 'sql' => 'image_url']], $sql);
            $sql['type']=input('post.type');
            $sql['pay']=input('post.pay');
            $sql['mark']=input('post.mark');
            $sql['status']= input('post.status')=='true'?:0;
            $sql['introduction']=input('post.introduction');
            $sql['release_time']=input('post.release_time');
            $sql['uid']=$_COOKIE['uid'];
            if ($ret === true) {
                $retJson = ['code' => 1, 'msg' => '成功'];
                DB('video')->insert($sql);
            } else {
                $retJson['msg'] = $ret;
            }
        }
        return json($retJson);
    }

    public function delete($vid){
        DB('video')->delete(['vid'=>$vid]);
        $retJson = ['code' => 0, 'msg' => '删除成功'];
        header('Location: '.$_SERVER['HTTP_REFERER']?:url('admin/movie/index'));
        return json($retJson);
    }
}