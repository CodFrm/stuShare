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
        V()->assign('title', '首页');
        V()->display();
    }

    public function logout() {
        setcookie('token', '', 0, '/');
        header('Location: ' . url('index/login/login'));
    }

    public function setting() {
        V()->assign('title', '设置页面');
        $setting['pc_update_v'] = config('pc_update_v');
        $setting['pc_update_u'] = config('pc_update_u');
        $setting['movie_app_update_v'] = config('movie_app_update_v');
        $setting['movie_app_update_u'] = config('movie_app_update_u');
        V()->assign('setting', $setting);
        V()->display();
    }

    public function u_setting() {
        if (input('post.pc_update_u')) {
            config('pc_update_u', input('post.pc_update_u'));
        }
        if (input('post.pc_update_v')) {
            config('pc_update_v', input('post.pc_update_v'));
        }
        if (input('post.movie_app_update_u')) {
            config('movie_app_update_u', input('post.movie_app_update_u'));
        }
        if (input('post.movie_app_update_v')) {
            config('movie_app_update_v', input('post.movie_app_update_v'));
        }
        return json(['code' => 1, 'msg' => '修改成功']);
    }

}