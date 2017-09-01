<?php
/**
 *============================
 * author:Farmer
 * time:2017/9/1 12:59
 * blog:blog.icodef.com
 * function:
 *============================
 */

namespace app\admin\ctrl;


use app\common\ctrl\auth;

class setting extends auth {
    public function update() {
        V()->assign('title', '更新设置');
        $setting['pc_update_v'] = config('pc_update_v');
        $setting['pc_update_u'] = config('pc_update_u');
        $setting['movie_update_v'] = config('movie_update_v');
        $setting['movie_update_u'] = config('movie_update_u');
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
        if (input('post.movie_update_u')) {
            config('movie_update_u', input('post.movie_update_u'));
        }
        if (input('post.movie_update_v')) {
            config('movie_update_v', input('post.movie_update_v'));
        }
        return json(['code' => 1, 'msg' => '修改成功']);
    }

    public function notice() {
        V()->assign('title', '通知设置');
        $setting['pc_notice_msg'] = config('pc_notice_msg');
        $setting['pc_notice_time'] = date('Y/m/d H:i:s', config('pc_notice_time'));
        $setting['movie_notice_msg'] = config('movie_notice_msg');
        $setting['movie_notice_time'] = date('Y/m/d H:i:s', config('movie_notice_time'));
        V()->assign('setting', $setting);
        V()->display();
    }

    public function notice_setting() {
        if (input('post.pc_notice_msg')) {
            config('pc_notice_msg', input('post.pc_notice_msg'));
            config('pc_notice_time', time());
        }
        if (input('post.movie_notice_msg')) {
            config('movie_notice_msg', input('post.movie_notice_msg'));
            config('movie_notice_time', time());
        }
        return json(['code' => 1, 'msg' => '修改成功']);
    }

}