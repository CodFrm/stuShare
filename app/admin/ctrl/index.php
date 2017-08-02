<?php
/**
 *============================
 * author:Farmer
 * time:2017/7/7 22:46
 * blog:blog.icodef.com
 * function:
 *============================
 */

namespace app\admin\ctrl;


use app\common\ctrl\auth;

class index extends auth {
    public function index() {
        V()->assign('title','首页');
        V()->display();
    }

    public function logout() {
        setcookie('token', '', 0, '/');
        header('Location: ' . url('index/login/login'));
    }
    public function setting(){
        V()->assign('title','设置页面');
        $setting['update_v']=config('update_v');
        $setting['update_u']=config('update_u');
        V()->assign('setting',$setting);
        V()->display();
    }
    public function u_setting(){
        if(input('post.update_u')){
            config('update_u',input('post.update_u'));
        }
        if(input('post.update_v')){
            config('update_v',input('post.update_v'));
        }
        return json(['code'=>1,'msg'=>'修改成功']);
    }

}