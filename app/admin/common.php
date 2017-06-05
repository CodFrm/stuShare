<?php
/**
 *============================
 * author:Farmer
 * time:2017/6/5 13:29
 * blog:blog.icodef.com
 * function:
 *============================
 */


/**
 * 通过uid获取用户信息
 * @author Farmer
 * @param $uid
 * @return mixed
 */
function uidUser($uid){
    return DB('user')->find(['uid'=>$uid]);
}