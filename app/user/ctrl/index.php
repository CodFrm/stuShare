<?php
/**
 *============================
 * author:Farmer
 * time:2017/6/5 0:00
 * blog:blog.icodef.com
 * function:
 *============================
 */

namespace app\user\ctrl;


use app\common\ctrl\auth;

class index extends auth {
    public function index() {
        V()->assign('title', '用户主页');
        $group = '';
        foreach ($this->userMsg['group'] as $item) {
            if ($item['expire_time'] == -1) {
                $group .= $item['group_name'] . ' 永久 ';
            } else if ($item['expire_time'] > time()) {
                $group .= $item['group_name'] . ' ' . date('Y-m-d h:i:s', $item['expire_time']) . '到期 ';
            }
        }
        V()->assign('group', $group);
        V()->display();
    }

    public function readme() {
        V()->assign('title', '说明文档');
        V()->display();
    }

    public function download() {
        if (stripos($_SERVER['HTTP_USER_AGENT'], 'win') > 0) {
            header('Location: ' . __HOME_ . '/static/win.zip');
        } else if (stripos($_SERVER['HTTP_USER_AGENT'], 'android') > 0) {
            header('Location: ' . __HOME_ . '/static/openvpn.apk');
        } else {
            echo '抱歉,暂未提供该种设备的下载链接,可以在首页反馈';
        }
    }

    public function serverlist() {
        V()->assign('title', '服务器列表');
        V()->display();
    }

    public function feedback(){
        V()->assign('title', '问题反馈');
        V()->display();
    }
}