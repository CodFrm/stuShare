<?php
/**
 *============================
 * author:Farmer
 * time:2017/6/8 21:50
 * blog:blog.icodef.com
 * function:客户端调用接口
 *============================
 */

namespace app\user\ctrl;


use app\common\ctrl\auth;

class api extends auth {
    /**
     * 获取在线用户
     * @author Farmer
     */
    public function online($ip='') {
        if($ip=''){

        }
        $data = DB(':radacct')->select(['acctstoptime is null'], 'count(*)')->fetch();
        $count = $data['count(*)'];
        $record = DB(':radacct')->select(['acctstoptime is null']);
        $rows = [];
        while ($row = $record->fetch()) {
            $tmp['username'] = $row['username'];
            $tmp['acctstarttime'] = $row['acctstarttime'];
            $rows[] = $tmp;
        }
        return json(['code' => 0, 'count' => $count, 'rows' => $rows]);
    }
}