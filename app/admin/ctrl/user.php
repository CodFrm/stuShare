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


class user {
    public function index(){
        header('Location: '.url('admin/index/index'));
    }
}