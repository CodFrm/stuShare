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

}