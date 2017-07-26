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
        V()->assign('title','用户主页');
        $group='';
        foreach ($this->userMsg['group'] as $item){
            if($item['expire_time']==-1){
                $group.=$item['group_name'].' 永久 ';
            }else if ($item['expire_time']>time()){
                $group.=$item['group_name'].' '.date('Y-m-d h:i:s',$item['expire_time']).'到期 ';
            }
        }
        V()->assign('group',$group);
        V()->display();
    }
}