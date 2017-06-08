<?php
/**
 *============================
 * author:Farmer
 * time:2017/6/8 21:50
 * blog:blog.icodef.com
 * function:客户端调用接口
 *============================
 */

namespace app\admin\ctrl;


use app\common\ctrl\auth;

class api extends auth {
    /**
     * 获取在线用户
     * @author Farmer
     */
    public function online(){
        $data=DB(':radacct')->select(['acctstoptime is null'],'count(*)')->fetch();
        return json(['code'=>0,'count'=>$data['count(*)']]);
    }
}