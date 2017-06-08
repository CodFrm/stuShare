<?php
/**
 *============================
 * author:Farmer
 * time:2017/6/5 14:17
 * blog:blog.icodef.com
 * function:
 *============================
 */

namespace app\admin\ctrl;


use app\common\ctrl\auth;

class money extends auth {
    public function recharge() {
        V()->assign('title', '充值页面');
        V()->display();
    }

    private $iplist = ['127.0.0.1','localhost'];

    public function pay_call() {
        if (in_array(getIP(), $this->iplist)) {
            $ret = isExist($_GET, [
                'order' => ['msg' => '请输入订单号', 'sql' => 'order_number'],
                'money' => ['msg' => '请输入金额', 'sql' => 'order_money'],
                'remarks' => ['msg' => '请输入备注', 'sql' => 'order_remarks']
            ], $data);
            if ($ret === true) {
                $data['order_time'] = time();
                if (DB('order')->insert($data) > 0) {
                    if ($userMsg = getUser($_GET['remarks'])) {
                        $this->money_change($userMsg['uid'], (double)$_GET['money'], '充值金额' . (double)$_GET['money'] . '元');
                    }
                }
                $ret = 'success';
            }
            echo $ret;
        }
        //将到期用户更改用户组 密码
        $red = DB('user')->select(['expire_time' => [time(), '<']]);
        while ($row = $red->fetch()) {
            DB(':radusergroup')->update(['groupname' => 'VIP0'], ['username' => $row['user']]);
            DB(':radcheck')->update(['op' => '='], ['username' => $row['user']]);
        }
    }

    private function money_change($uid, $change, $log) {
        $this->wlog($uid, $log, 10);
        if ($change > 0) {
            DB('user')->update('`money`=`money`+' . $change, ['uid' => $uid]);
        } else {
            DB('user')->update('`money`=`money`-' . abs($change), ['uid' => $uid]);
        }
    }

    public function vip() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $json = ['code' => -1, 'msg' => '系统错误'];
            $month = ceil(input('post.month'));
            if ($month <= 0) {
                $json = ['code' => -1, 'msg' => '请输入正确的月数'];
            } else if ($userMsg = uidUser($_COOKIE['uid'])) {
                $month_money = 20;
                if ($userMsg['money'] < $month * $month_money) {
                    $json = ['code' => -1, 'msg' => '余额不足,差' . (($month * $month_money) - $userMsg['money']) . '元'];
                } else {
                    $json = ['code' => 0, 'msg' => '续费成功'];
                    $extime = 0;
                    $this->money_change($_COOKIE['uid'], -($month * $month_money), '续费VIP消费' . ($month * $month_money) . '元');
                    if ($userMsg['expire_time'] < time()) {//到期的
                        $extime = time() + $month * 2592000;
                    } else {//续费的
                        $extime = $userMsg['expire_time'] + $month * 2592000;
                    }
                    DB(':radusergroup')->update(['groupname' => 'VIP1'], ['username' => $userMsg['user']]);
                    DB('user')->update(['expire_time' => $extime], ['uid' => $userMsg['uid']]);
                    DB(':radcheck')->update(['op' => ':=', ['username' => $userMsg['user']]]);
                }
            }
            return json($json);
        } else {
            V()->assign('title', '开通VIP');
            V()->display();
        }
    }
}