<?php
/**
 *============================
 * author:Farmer
 * time:2017/8/22 12:44
 * blog:blog.icodef.com
 * function:
 *============================
 */

namespace app\admin\ctrl;


use app\common\ctrl\auth;

class user extends auth {
    public function index($page=1){
        $total=50;
        V()->assign('title', '影视管理');
        $rec = DB('user as a')->select(['__order by'=>'uid desc', '__limit' => (($page - 1) * $total) . ','.$total]);
        $user_list = $rec->fetchAll();
        $count=DB('user')->find([],'count(*)')['count(*)'];
        V()->assign('page',$page);
        V()->assign('pageAll',ceil($count/$total));
        V()->assign('user_list', $user_list);
        V()->display();
    }

    public function edit($uid){

    }
    public function delete($uid){
        DB('user')->delete(['uid'=>$uid]);
        $retJson = ['code' => 0, 'msg' => '删除成功'];
        header('Location: '.$_SERVER['HTTP_REFERER']?:url('admin/user/index'));
        return json($retJson);
    }

    public function feedback($page=1){
        $total=50;
        V()->assign('title', '用户反馈');
        $rec = DB('feedback as a')->select(['__order by'=>'time desc', '__limit' => (($page - 1) * $total) . ','.$total],'*',
            'join :user as b on a.uid=b.uid');
        $feed_list = $rec->fetchAll();
        $count=DB('user')->find([],'count(*)')['count(*)'];
        V()->assign('page',$page);
        V()->assign('pageAll',ceil($count/$total));
        V()->assign('feed_list', $feed_list);
        V()->display();
    }
    public function look($uid=0,$time=0){
        DB('feedback')->update(['`type`=-`type`'],['uid'=>$uid,'time'=>$time]);
        header('Location: '.$_SERVER['HTTP_REFERER']?:url('admin/movie/index'));
    }
}