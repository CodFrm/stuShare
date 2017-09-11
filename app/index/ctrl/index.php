<?php

/**
 *============================
 * author:Farmer
 * time:2017/6/4 21:00
 * blog:blog.icodef.com
 * function:
 *============================
 */

namespace app\index\ctrl;
class index {
    function index() {
        V()->display();
    }

    function error() {
        V()->assign('title', '错误页面');
        V()->assign('url', input('get.url'));
        V()->assign('error', input('get.error'));
        V()->display();
    }

    function ip() {
        return getIP();
    }
}