<?php
/**
 *============================
 * author:Farmer
 * time:2017/3/29 20:29
 * blog:blog.icodef.com
 * function:全部的公共的函数库
 *============================
 */


/**
 * 获取token
 * @author Farmer
 * @param $uid
 * @return string
 */
function getToken($uid) {
    $token = getRandString(8, 2) . time();
    DB('token')->insert(['uid' => $uid, 'token' => $token, 'time' => time()]);
    DB('token')->delete(['time<'.(time().-604800)]);
    return $token;
}

/**
 * 验证token
 * @author Farmer
 * @param $uid
 * @param $token
 * @return bool
 */
function verifyToken($uid, $token) {
    $where=['token'=>$token,'uid'=>$uid];
    $tokenMsg = DB('token')->select($where)->fetch();
    if (!$tokenMsg) {
        return false;
    } else if ($tokenMsg['time'] + 604800 < time()) {
        DB('token')->delete($where);
        return false;
    }
    DB('token')->update(['time'=>time()],$where);
    return true;
}

/**
 * 获取ip
 * @author Farmer
 * @return array|false|string
 */
function getIP() {
    if (getenv('HTTP_CLIENT_IP')) {
        $ip = getenv('HTTP_CLIENT_IP');
    }
    elseif (getenv('HTTP_X_FORWARDED_FOR')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    }
    elseif (getenv('HTTP_X_FORWARDED')) {
        $ip = getenv('HTTP_X_FORWARDED');
    }
    elseif (getenv('HTTP_FORWARDED_FOR')) {
        $ip = getenv('HTTP_FORWARDED_FOR');

    }
    elseif (getenv('HTTP_FORWARDED')) {
        $ip = getenv('HTTP_FORWARDED');
    }
    else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
/**
 * 取随机字符串
 * @author Farmer
 * @param $length
 * @param $type
 * @return string
 */
function getRandString($length, $type) {
    $randString = '1234567890qwwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHHJKLZXCVBNM';
    $retStr = '';
    for ($n = 0; $n < $length; $n++) {
        $retStr .= substr($randString, rand(0, 10 + $type * 24), 1);
    }
    return $retStr;
}

/**
 * 通过 email user 获取用户信息
 * @author Farmer
 * @param $user
 * @return bool|mixed
 */
function getUser($user) {
    if ($userMsg = DB('user')->find(['user' => $user,'email'=>[$user,'or']])) {
        return $userMsg;
    }
    return false;
}