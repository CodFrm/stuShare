<?php
/**
 *============================
 * author:Farmer
 * time:2017/6/5 14:17
 * blog:blog.icodef.com
 * function:
 *============================
 */

namespace app\user\ctrl;


use app\common\ctrl\auth;

class money extends auth {
    public function recharge() {
        V()->assign('title', '充值页面');
        V()->display();
    }

    private $iplist = ['127.0.0.1', 'localhost', '::1'];

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
                        sendEmail($userMsg['email'],'账号充值成功 - 信院小站',
                            "<body style=\"margin:0;\">
<div style=\"width:100%;height:100%;min-height:600px;\">
    <img src=\"http://s.icodef.com/static/image/email_bg.jpg\" style=\"width:100%;height:100%;position: absolute;z-index: -1;\">
    <div style=\"color: rgba(255, 0, 0, 1);margin-left: 15%;width: 70%;padding:20px\">
        <div style=\"padding:10px;background: rgba(160, 160, 160, 0.5);border-radius: 5px;\">
                <h2>您的账号{$userMsg['user']}充值成功,充值金额:{$_GET['money']}</h2>
                <br><h3>若有误差,或者并未到账,请在反馈中反馈,客服会尽快处理</h3>
                <h3><a href='http://sv.icodef.com/user/index/index'>个人主页</a> </h3>
        </div>
    </div>
</div>
</body>");
                    }
                }
                $ret = 'success';
            }
            echo $ret;
        }
        //定时计划,执行邮件发送
        if (config('email') < time() - 3600) {
            set_time_limit(0);
            ignore_user_abort(true);
            config('email', time());
            //检查到期 7天 3天 1天 over
//            $this->check_expire(time() + 604800, time() + 259200, 7);
            $this->check_expire(time() + 259100, time(), 3);
//            $this->check_expire(time() + 86300, time(), 1);
        }
    }

    private function check_expire($expire_time, $range, $day) {
        $rec = DB('usergroup as a')->select(['expire_time' => [$expire_time, '<'], 'expire_time!=-1 and expire_time>' . $range], '*',
            'join :group as b on a.group_id=b.group_id join :user as c on a.uid=c.uid join :set_meal as d on d.group_id=b.group_id');
        $rows = $rec->fetchAll();
        foreach ($rows as $item) {
            if ($this->isSend($item['uid'], time() - $day * 86400, $day)) {
                sendEmail($item['email'], '到期通知 - 信院小站',
                    "<body style=\"margin:0;\">
<div style=\"width:100%;height:100%;min-height:600px;\">
    <img src=\"http://s.icodef.com/static/image/email_bg.jpg\" style=\"width:100%;height:100%;position: absolute;z-index: -1;\">
    <div style=\"color: rgba(255, 0, 0, 1);margin-left: 15%;width: 70%;padding:20px\">
        <div style=\"padding:10px;background: rgba(160, 160, 160, 0.5);border-radius: 5px;\">
                <h2>您的账号还有{$day}天到期</h2><br/>到期时间:" . date("Y/m/d H:i:s", $item['expire_time']) .
                    "<br/>续费链接:".url('user/money/vip') ."</h2>
                <br> <h3>现在的套餐与资费:{$item['group_name']} {$item['set_meal_money']}元/月</h3>
                <h3><a href='http://sv.icodef.com/user/money/recharge'>前往充值</a></h3>
        </div>
    </div>
</div>
</body>");
            }
        }
        return $rows;
    }

    private function isSend($uid, $time, $type) {
        $rec = DB('send_email')->find(['uid' => $uid, 'type' => $type, '__order' => ' by time desc']);
        if ($rec) {//查询到了,看看有没有过期
            if ($rec['time'] < $time) {
                DB('send_email')->update(['uid' => $uid, 'time' => time(), 'type' => $type], ['uid' => $uid, 'time' => $rec['time'], 'type' => $type]);
                return true;
            }
            return false;
        }
        DB('send_email')->insert(['uid' => $uid, 'time' => time(), 'type' => $type]);
        return true;
    }

    private function email_notice() {
        set_time_limit(0);
        ignore_user_abort(true);
        $rows = DB('user')->select()->fetchAll();
        foreach ($rows as $item) {
            sendEmail($item['email'], "电信线路开通啦(三号) - 信院小站", "<body style=\"margin:0;\">
<div style=\"width:100%;height:100%;min-height:600px;\">
    <img src=\"http://s.icodef.com/static/image/email_bg.jpg\" style=\"width:100%;height:100%;position: absolute;z-index: -1;\">
    <div style=\"color: rgba(255, 0, 0, 1);margin-left: 15%;width: 70%;padding:20px\">
        <div style=\"padding:10px;background: rgba(160, 160, 160, 0.5);border-radius: 5px;\">
                <h1>大家期盼已久的电信来了~</h1>
                <h2>电信线路已经开通</h2>
                <h2>速度将更将流畅,游戏延迟更低,五杀吃鸡随手拈来</h2>
                <h2>手机配置文件下载:<a href='http://sv.icodef.com/user/api/downconfig?svid=5'>三号线路(电信)</a>请先连接校园网</h2>
                <br>
                <h3>(以上内容是乱编的,我也不知道体验咋样2333,用了之后大家可以使用反馈功能反馈,或者在群里AT我^_^)</h3>
        </div>
    </div>
</div>
</body>");
            echo $item['email'];
        }
    }

    private function money_change($uid, $change, $log) {
        if ($change > 0) {
            DB('user')->update('`money`=`money`+' . $change, ['uid' => $uid]);
            $this->wlog($uid, $log,$change, 10);
        } else {
            DB('user')->update('`money`=`money`-' . abs($change), ['uid' => $uid]);
            $this->wlog($uid, $log,$change, 11);
        }
    }

    public function vip() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $json = ['code' => -1, 'msg' => '系统错误'];
            $month = ceil(input('post.month'));
            if ($month <= 0) {
                $json = ['code' => -1, 'msg' => '请输入正确的月数'];
            } else if ($userMsg = uidUser($_COOKIE['uid'])) {
                $tid = input('post.tid');
                $row = DB('set_meal as a')->find(['tid' => $tid], '*', 'join ' . input('config.DB_PREFIX') . 'group as b on a.group_id=b.group_id');
                if (!$row) {
                    $json = ['code' => -1, 'msg' => '未找到套餐'];
                } else {
                    $month_money = $row['set_meal_money'];
                    $newTime = $month * 2592000;
                    $subMoney = $month * $month_money;
                    $group_id = $row['group_id'];
                    if ($userMsg['money'] < $subMoney) {
                        $json = ['code' => -1, 'msg' => '余额不足,差' . (($subMoney) - $userMsg['money']) . '元'];
                    } else {
                        $extime = 0;
                        $oldSm = getUserSetMeal($_COOKIE['uid'], input('post.tid'));
                        if ($oldSm) {//判断有没有开通服务
                            if ($oldSm['tid'] != $tid) {//不是同一套餐,将老套餐的剩余时间换算成新的减一天
                                $lastTime = $oldSm['expire_time'] - time();
                                $lastMoney = $lastTime * ($oldSm['set_meal_money'] / 2592000);
                                $addTime = $lastMoney / ($month_money / 2592000) - 86400;
                                if ($addTime < 0) {
                                    $addTime = 0;
                                }
                                $newTime += $addTime;
                            }
                            if ($oldSm['expire_time'] < time()) {//到期续期
                                $extime = time() + $newTime;
                            } else {//增加时间
                                $extime = $oldSm['expire_time'] + $newTime;
                            }
                            DB('usergroup')->update(['group_id' => $group_id, 'expire_time' => $extime], ['uid' => $_COOKIE['uid'], 'group_id' => $oldSm['group_id']]);
                        } else {
                            $extime = time() + $newTime;
                            DB('usergroup')->insert(['uid' => $_COOKIE['uid'], 'group_id' => $group_id,
                                'expire_time' => (time() + $newTime)]);
                        }
                        $this->money_change($_COOKIE['uid'], -$subMoney, '续费VIP消费' . $subMoney . '元');
                        return json(['code' => 0, 'msg' => '续费成功,充值套餐:' . $row['group_name'] . ' 到期日期:' . date('Y/m/d H:i:s', $extime)]);
                    }
                }
            }
            return json($json);
        } else {
            $set_meal = DB('set_meal as a')->select(0, '*', 'join ' . input('config.DB_PREFIX') . 'group as b on a.group_id=b.group_id')->fetchAll();
//            print_r($set_meal);
            V()->assign('set_meal', $set_meal);
            V()->assign('title', '开通VIP');
            V()->display();
        }
    }
}