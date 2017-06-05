<?php
/**
 *============================
 * author:Farmer
 * time:2017/6/5 0:00
 * blog:blog.icodef.com
 * function:
 *============================
 */

namespace app\admin\ctrl;


use app\common\ctrl\auth;

class index extends auth {
    public function index() {
        V()->assign('title','用户主页');
        V()->display();
    }
}