<?php
/**
 *============================
 * author:Farmer
 * time:2017/6/8 21:50
 * blog:blog.icodef.com
 * function:客户端调用接口(需要登录)
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
     * 获取服务器列表
     * @author Farmer
     */
    public function getserver() {
        $rec = DB('server')->select();
        $ret = ['code' => 0, 'msg' => 'success'];
        foreach ($rec->fetchAll() as $item) {
            $tmp['svid']=$item['svid'];
            $tmp['name'] = $item['name'];
            $tmp['ip'] = $item['ip'];
            $tmp['config'] = $item['config'];
            $tmp['count'] = DB("accounting")->select(['nas_ip' => $item['ip'], 'logout_time' => '-1'], 'count(*)')->fetch()['count(*)'];
            $ret['rows'][] = $tmp;
        }
        return json($ret);
    }

    /**
     * 下载服务器配置
     * @author Farmer
     * @param int $svid
     */
    public function downconfig($svid=0){
        $row=DB('server')->find(['svid'=>$svid]);
        if($row){
            header('Content-Type:application/text');
            header('Content-Disposition: attachment; filename="'.$row['name'].'.ovpn"');
            header('Content-Length:'.strlen($row['config']));
            echo $row['config'];
        }else{
            echo 'svid 错误';
        }
    }
    /**
     * 获取权限
     * @author Farmer
     */
    public function getauth() {
        $ret = ['code' => 0, 'msg' => 'success','user'=>$this->userMsg['user']];
        $ret['rows'] = $this->userMsg['group'];
        return json($ret);
    }

    public function feedback() {
        $ret = isExist($_POST, [
            'call' => '请输入联系方式,不想输入就随便打吧,反正系统会记录的',
            'msg' => '必须输入你想说的东西!'
        ]);
        $retJson = ['code' => -1];
        if (isset($_POST['type'])) {
            $type = $_POST['type'];
        } else {
            $type = 0;
        }
        if($type!=1 or $type!=2 or $type!=3){
            $retJson['msg'] = $ret;
            if ($ret === true) {
                if(strlen($_POST['msg'])<20){
                    $retJson = ['code' => -1, 'msg' => '你就这么点想说的?'];
                }else {
                    $retJson = ['code' => 0, 'msg' => '反馈成功'];
                    DB('feedback')->insert(['uid' => $_COOKIE['uid'], 'contact' => $_POST['call'],
                        'msg' => $_POST['msg'], 'time' => time(), 'type' => $type]);
                }
            }
            return json($retJson);
        }
        return '';
    }
}