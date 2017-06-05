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
    static $whitelist = ['pay_call'];
    public function __construct() {
        if (!in_array(input('action'), auth::$whitelist)) {
            if (!isset($_COOKIE['uid']) || !isset($_COOKIE['token'])) {
                header('Location:'.url('index/index/error','error=请登录后操作&url='.url('index/login/login')));
                exit();
            } else if (!verifyToken($_COOKIE['uid'], $_COOKIE['token'])) {
                header('Location:'.url('index/index/error','error=请登录后操作&url='.url('index/login/login')));
                exit();
            }
            $userMsg=uidUser($_COOKIE['uid']);
            V()->assign('user',$userMsg['user']);
            V()->assign('money',$userMsg['money']);
            V()->assign('expire',($userMsg['expire_time']<time()?'已过期':date('Y-m-d h:i:s',$userMsg['expire_time'])));
            V()->assign('grade',$userMsg['grade']);
        }
    }

    /**
     * 日志入库  10 金额充值
     * @author Farmer
     * @param $uid
     * @param $log
     * @param int $type
     */
    public function wlog($uid,$log,$type=0){
        $req='get:'.implodes(',',$_GET);
        $req.='post:'.implodes(',',$_POST);
        $req.='ip:'.getIP();
        DB('log')->insert(array('log'=>$log,'log_req'=>$req,'log_uid'=>$uid,'log_time'=>time(),'log_type'=>$type));
    }
}
function implodes($glue,$array){
    $ret='';
    foreach($array as $key=>$value){
        $ret.="$key=>$value".',';
    }
    return $ret;
}