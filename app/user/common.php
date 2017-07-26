<?php
/**
 *============================
 * author:Farmer
 * time:2017/6/5 13:29
 * blog:blog.icodef.com
 * function:
 *============================
 */


function isActive($ctrl){
    if(is_array($ctrl)){
        foreach ($ctrl as $item){
            if($item==input('ctrl')){
                return ' active';
            }
        }
    }else if($ctrl==input('ctrl')){
        return ' active';
    }
    return '';
}