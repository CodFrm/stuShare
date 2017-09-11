<?php
/**
 *============================
 * author:Farmer
 * time:2017/7/26 10:05
 * blog:blog.icodef.com
 * function:
 *============================
 */

function feedType($type){
    $ret='';
    if(abs($type)==1){
        $ret= '网络';
    }else if(abs($type)==2){
        $ret= '影视';
    }
    return $ret.($type<=0?'(已阅)':'');
}