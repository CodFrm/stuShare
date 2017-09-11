<?php
/**
 *============================
 * author:Farmer
 * time:2017/9/1 14:01
 * blog:blog.icodef.com
 * function:提供给客户端使用的接口(无须登录)
 *============================
 */

namespace app\index\ctrl;


class api {

    /**
     * 软件更新信息获取 影视
     * @author Farmer
     */
    public function update_m() {
        $ret['v'] = config('movie_update_v');
        $ret['u'] = config('movie_update_u');
        return json($ret);
    }

    /**
     * 软件更新信息获取 win 校园网客户端
     * @author Farmer
     */
    public function update_pc() {
        $ret['v'] = config('pc_update_v');
        $ret['u'] = config('pc_update_u');
        return json($ret);
    }

    /**
     * 通知获取 影视
     * @author Farmer
     * @return string
     */
    public function notice_m() {
        $ret['msg'] = config('movie_notice_msg');
        $ret['t'] = config('movie_notice_time');
        return json($ret);
    }

    /**
     * 通知获取 win 校园网客户端
     * @author Farmer
     * @return string
     */
    public function notice_pc() {
        $ret['msg'] = config('pc_notice_msg');
        $ret['t'] = config('pc_notice_time');
        return json($ret);
    }

}