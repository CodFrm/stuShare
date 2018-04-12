<?php
/**
 *============================
 * author:Farmer
 * time:2017/3/29 20:12
 * blog:blog.icodef.com
 * function:
 *============================
 */

namespace app\common\ctrl;

class auth {
    static $whitelist = ['pay_call', 'mlist', 'volume', 'movie_vip'];
    protected $userMsg;

    public function __construct() {
        if (!in_array(input('action'), auth::$whitelist)) {
            if (!isset($_COOKIE['uid']) || !isset($_COOKIE['token'])) {
                header('Location:' . url('index/index/error', 'error=请登录后操作&url=' . url('index/login/login')));
                exit();
            } else if (!verifyToken($_COOKIE['uid'], $_COOKIE['token'])) {
                header('Location:' . url('index/index/error', 'error=请登录后操作&url=' . url('index/login/login')));
                exit();
            }
            $this->userMsg = uidUser($_COOKIE['uid']);
            $this->userMsg['group'] = getGroup($_COOKIE['uid']);
            foreach ($this->userMsg['group'] as $item) {
                if (isAuth($item['group_id'])) {
                    $auth = true;
                    break;
                }
            }
            if ($auth !== true) {
                header('Location:' . url('index/index/error', 'error=你没有相应的权限&url=' . url('user/index/index')));
                exit();
            }
            V()->assign('user', $this->userMsg['user']);
            V()->assign('money', $this->userMsg['money']);
            V()->assign('ctrl', input('ctrl'));
            V()->assign('action', input('action'));
        }
    }

    /**
     * 日志入库  10 金额充值
     * @author Farmer
     * @param $uid
     * @param $log
     * @param int $type
     */
    public function wlog($uid, $log, $param, $type = 0) {
        $req = 'get:' . implodes(',', $_GET);
        $req .= ' post:' . implodes(',', $_POST);
        $req .= ' ip:' . getIP();
        DB('log')->insert(array('log' => $log, 'log_req' => $req, 'log_param' => $param, 'log_uid' => $uid, 'log_time' => time(), 'log_type' => $type));
    }
}

function implodes($glue, $array) {
    $ret = '';
    foreach ($array as $key => $value) {
        $ret .= "$key=>$value" . $glue;
    }
    return $ret;
}