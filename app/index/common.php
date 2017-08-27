<?php
/**
 *============================
 * author:Farmer
 * time:2017/6/4 23:03
 * blog:blog.icodef.com
 * function:
 *============================
 */

/**
 * 验证邀请码
 * @author Farmer
 * @param $inv
 * @return bool|string
 */
function isInvCode($inv) {
    if ($invMsg = DB('inv_code')->find(['inv_code' => $inv])) {
        if ($invMsg['inv_use_uid'] <= 0) {
            return true;
        } else {
            return '邀请码被用过了';
        }
    } else {
        return '邀请码不存在';
    }
}

/**
 * 验证用户名
 * @author Farmer
 * @param $user
 * @return bool|string
 */
function isUser($user) {
    if (getUser($user)) {
        return '用户名已经被注册';
    } else {
        return true;
    }
}

/**
 * 验证游戏
 * @author Farmer
 * @param $user
 * @return bool|string
 */
function isEmail($email) {
    if (getUser($email)) {
        return '邮箱已经被注册';
    } else {
        return true;
    }
}


/**
 * 验证IP
 * @author Farmer
 * @param $ip
 * @return bool
 */
function verifyIP($ip){
    if($ipMsg=DB('ip')->find(array('ip'=>$ip))){
        if($ipMsg['ip_time']<(time()-config('regip'))){
            DB('ip')->update(array('ip_time'=>time()),array('ip'=>$ip));
            return true;
        }else{
            return false;
        }
    }
    DB('ip')->insert(array('ip'=>$ip,'ip_time'=>time()));
    return true;
}
