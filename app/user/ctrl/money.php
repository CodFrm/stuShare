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
use icf\lib\db;

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
                        sendEmail($userMsg['email'], '账号充值成功 - 信院小站',
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
                    "<br/>续费链接:" . url('user/money/vip') . "</h2>
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
            $this->wlog($uid, $log, $change, 10);
        } else {
            DB('user')->update('`money`=`money`-' . abs($change), ['uid' => $uid]);
            $this->wlog($uid, $log, $change, 11);
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
                            $extime = time() + $newTime;
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

    private function hk() {
        $req = DB()->query("SELECT
	*
FROM
	share_usergroup AS a
JOIN share_set_meal AS b ON a.group_id = b.group_id
WHERE
	(
		a.expire_time =- 1
		OR a.expire_time > " . time() . "
	)
AND a.group_id =b.group_id
order BY a.expire_time desc");
        set_time_limit(0);
        foreach ($req->fetchAll() as $key => $value) {
            if ($value['expire_time'] < 1516270699 && $value['expire_time'] != -1) {
                DB('usergroup')->update(['expire_time' => 1516270698], ['uid' => $value['uid'],
                    'group_id' => $value['group_id']]);
            }
            echo "uid:{$value['uid']}<br/>";
            self::statistics($value['uid']);
        }

    }

    private static function statistics($uid = 0) {
        $user = uidUser($uid);
        V()->assign('user', $user['user']);
        V()->assign('time', date('Y年m月d日', $user['reg_time']));
        V()->assign('day', round((time() - $user['reg_time']) / 86400));
        $req = DB()->query("SELECT
	sum(out_byte) + sum(input_byte) AS `all`,
	sum(out_byte) AS `out`,
	sum(input_byte) AS `in`,
	SUM(a.logout_time - a.login_time) AS all_time,
	(
		sum(out_byte) + sum(input_byte)
	) DIV (
		SUM(a.logout_time - a.login_time)
	) AS pj_all,
	(sum(input_byte)) DIV (
		SUM(a.logout_time - a.login_time)
	) AS pj_in,
	(sum(out_byte)) DIV (
		SUM(a.logout_time - a.login_time)
	) AS pj_out
FROM
	share_accounting AS a
WHERE
	uid = $uid AND a.logout_time - a.login_time > 0;");
        $row = $req->fetch();
        V()->assign('all', round($row['all'] / 1073741824));
        V()->assign('d', round($row['out'] / 1073741824));
        V()->assign('u', round($row['in'] / 1073741824));
        $value = round(($row['all'] / 1048576) * 0.2);
        V()->assign('rmb', $value);
        V()->assign('rmb_bq', self::RangeQ($value, [
            0 => '为你节省了一顿饭钱(OS:你怎么用得这么少)', 50 => '为你节省了一天的饭钱(OS:你怎么用得这么少)', 100 => '太少了,不算了',
            1000 => '相当于一个月生活费', 2000 => '相当于一个月生活费', 5000 => '为你屯了一仓库的零食', 7000 => '为你省下了一台笔记本电脑',
            10000 => '帮你保住了一个肾(iPhoneX)', 25000 => '帮你买了块Watch', 50000 => '帮你攒了一颗钻戒', 100000 => '为你省下一台五菱神光', 10000000 => '肯定系统出BUG了'
        ]));
        $value = round($row['pj_all'] / 1024);
        V()->assign('s_kb', $value);
        V()->assign('s_kb_pd', self::RangeQ($value, [
            0 => '看你这样应该经常玩游戏吧,要注意掌握时间哦', 30 => '看你这样应该经常玩游戏吧,要注意掌握时间哦',
            85 => '', 10000 => '你是不是喜欢看电影或者下载之类的啊?我也是唉,所以下学习准备搭建高速公(线)路,希望你还能够继续支持哦', 1000000 => '肯定系统出BUG了'
        ]));

        $req = DB()->query("SELECT
	b.`user`,
	elt(
		INTERVAL (
			MOD (a.login_time-57600, 86400),
			0,3600,7200,10800,14400,18000,21600,25200,28800,32400,36000,39600,43200,46800,50400,54000,57600,61200,64800,68400,72000,75600,79200,82800
		),'0点','1点','2点','3点','4点','5点','6点','7点','8点','9点','10点','11点','12点','13点','14点','15点','16点','17点','18点','19点','20点','21点','22点','23点'
	) AS h,
	count(a.aid) AS `count`,
	a.login_time
FROM
	share_accounting AS a join share_user as b on a.uid=b.uid
WHERE
	a.uid = $uid
GROUP BY
	elt(
		INTERVAL (
			MOD (a.login_time-57600, 86400),
			0,3600,7200,10800,14400,18000,21600,25200,28800,32400,36000,39600,43200,46800,50400,54000,57600,61200,64800,68400,72000,75600,79200,82800
		),'0点','1点','2点','3点','4点','5点','6点','7点','8点','9点','10点','11点','12点','13点','14点','15点','16点','17点','18点','19点','20点','21点','22点','23点'
	) order by `count` desc;");
        $rows = $req->fetchAll();
        if (count($rows) >= 2) {
            V()->assign('s_t', $rows[0]['h']);
            V()->assign('e_t', $rows[1]['h']);
        }
        if (self::owl($rows)) {
            V()->assign('l_t_ts', '您还经常在23点以后登录,看来您是一个夜猫子哦,希望您以后能够注意休息哦');
        }
        $row = DB('account as a')->query("select (a.logout_time - a.login_time) as t from share_accounting as a where uid=$uid and (a.logout_time - a.login_time)>=3600 order by t desc;")->fetch();
        $req = DB()->query("SELECT
	(a.logout_time - a.login_time) AS lt,
	elt(
		INTERVAL (
			MOD ((a.logout_time - a.login_time), 86400),
			3600,7200,10800,14400,18000,21600,25200,28800,32400,36000,39600,43200,46800,50400,54000,57600,61200,64800,68400,72000,75600,79200,82800
		),'1小时','2小时','3小时','4小时','5小时','6小时','7小时','8小时','9小时','10小时','11小时','12小时','13小时','14小时','15小时','16小时','17小时','18小时','19小时','20小时','21小时','22小时','23小时以上'
	) AS h,	count(a.aid) AS `count`
FROM
	share_accounting AS a
WHERE
	uid = $uid
and (a.logout_time - a.login_time)>=3600
GROUP BY
elt(
		INTERVAL (
			MOD ((a.logout_time - a.login_time), 86400),
			3600,7200,10800,14400,18000,21600,25200,28800,32400,36000,39600,43200,46800,50400,54000,57600,61200,64800,68400,72000,75600,79200,82800
		),'1小时','2小时','3小时','4小时','5小时','6小时','7小时','8小时','9小时','10小时','11小时','12小时','13小时','14小时','15小时','16小时','17小时','18小时','19小时','20小时','21小时','22小时','23小时以上'
	)
order by `count` desc");
        $rows = $req->fetchAll();
        V()->assign('m_t', round($row['t'] / 3600, 1));
        if (count($rows) >= 3) {
            for ($i = 0; $i < 3; $i++) {
                $tmp = str_replace('小时', '', $rows[$i]['h']);
                if ($tmp >= 3 || ($tmp == 2 && $rows[$i]['h'] >= 15)) {
                    V()->assign('m_t_ts', '经常在线三个小时以上,太久盯着屏幕也不好,请注意休息');
                    break;
                }
            }
        }

        V()->assign('n_time', date('Y/m/d H:i:s'));
        $html = V()->compile('statistics');
        sendEmail($user['email'], '您的新年贺卡到啦,2018新年快乐~ - 信院南站', $html);
    }

    private static function owl($arr) {
        $total = 0;
        $night = 0;
        foreach ($arr as $item) {
            $total += $item['count'];
            $tmp = str_replace('点', '', $item['h']);
            if ($tmp >= 23 || $tmp <= 3) {
                $night += $item['count'];
            }
        }
        if ($night <= 0 || $total <= 0) return false;
        if ($night > 25 || ($night / $total) > 0.4) {
            return true;
        }
        return false;
    }

    private static function RangeQ($value, $arr = []) {
        $lK = 0;
        foreach ($arr as $key => $item) {
            if ($value <= $key && $value >= $lK) {
                return $item;
            }
            $lK = $key;
        }
        return 'null';
    }
}