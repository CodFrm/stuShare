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
    public function online($ip = '') {
        $where = ['logout_time' => -1];
        if ($ip != '') {
            $where['nas_ip'] = $ip;
        }
        $data = DB('accounting')->select($where, 'count(*)')->fetch();
        $count = $data['count(*)'];
        $record = DB('accounting as a')->select($where, '*', 'join ' . input('config.DB_PREFIX') . 'user as b on a.uid=b.uid');
        $rows = [];
        while ($row = $record->fetch()) {
            $tmp['username'] = $row['user'];
            $tmp['login_time'] = $row['login_time'];
            $tmp['nip'] = $row['nas_ip'];
            $rows[] = $tmp;
        }
        return json(['code' => 0, 'count' => $count, 'rows' => $rows]);
    }

    /**
     * 软件更新信息获取
     * @author Farmer
     */
    public function update() {
        $ret['v'] = config('movie_app_update_v');
        $ret['u'] = config('movie_app_update_u');
        return json($ret);
    }

    /**
     * 获取服务器列表
     * @author Farmer
     */
    public function getserver() {
        $rec = DB('server')->select();
        $ret=['code' => 0,'msg'=>'success'];
        foreach ($rec->fetchAll() as $item){
            $tmp['name']=$item['name'];
            $tmp['ip']=$item['ip'];
            $tmp['config']=$item['config'];
            $ret['rows'][]=$tmp;
        }
        return json($ret);
    }
}