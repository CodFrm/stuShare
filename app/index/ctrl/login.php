<?php
/**
 *============================
 * author:Farmer
 * time:2017/6/4 22:39
 * blog:blog.icodef.com
 * function:
 *============================
 */

namespace app\index\ctrl;

class login {
    function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $json = ['code' => -1, 'msg' => '系统错误'];
            $ret = isExist($_POST, [
                'user' => ['regex' => ['/^[\x{4e00}-\x{9fa5}\w\@\.]{2,}$/u', '用户名不符合规则'], 'msg' => '请输入用户名', 'sql' => 'user'],//中文匹配头疼
                'pwd' => ['regex' => ['/^[\\~!@#$%^&*()-_=+|{}\[\], .?\/:;\'\"\d\w]{6,16}$/', '密码不符合规范'], 'msg' => '请输入密码', 'sql' => 'password'],
            ], $data);
            if ($ret === true) {
                if ($userMsg = getUser($_POST['user'])) {
                    if ($userMsg['password'] == $_POST['pwd']) {
                        setcookie('token', getToken($userMsg['uid']), time() + 86400, '/');
                        setcookie('uid', $userMsg['uid'], time() + 86400, '/');
                        $json['code'] = 0;
                        $json['msg'] = '登陆成功';
                    } else {
                        $json['code'] = -1;
                        $json['msg'] = '密码错误';
                    }
                } else {
                    $json['msg'] = '账号不存在';
                }
            } else {
                $json['msg'] = $ret;
            }
            return json($json);
        } else {
            V()->assign('title', '登陆页面');
            V()->display();
        }
    }

    function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $json = ['code' => -1, 'msg' => '系统错误'];
            $ret = isExist($_POST, [
                'user' => ['func' => ['isUser'], 'regex' => ['/^[\w]{2,10}$/', '用户名不符合规则'], 'msg' => '请输入用户名', 'sql' => 'user'],//中文匹配头疼
                'pwd' => ['regex' => ['/^[\\~!@#$%^&*()-_=+|{}\[\], .?\/:;\'\"\d\w]{6,16}$/', '密码不符合规范'], 'msg' => '请输入密码', 'sql' => 'password'],
                'email' => ['func' => ['isEmail'], 'regex' => ['/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', '邮箱不符合规则', 'msg' => '请输入邮箱'], 'sql' => 'email'],
            ], $data);
            if ($ret === true) {
                if (!(verifyIP(getIP()))) {
                    $json = ['code' => -1, 'msg' => '注册过于频繁'];
                } else {
                    $json['code'] = 0;
                    $json['msg'] = '注册成功,系统将会发送一封邮件到你的邮箱上,请点击邮箱激活账户';
                    $data['reg_time'] = time();
                    DB('user')->insert($data);
                    $uid = DB()->lastinsertid();
                    $code=getRandString(8, 0);
                    $url = url('index/login/email', 'code='.$code.'&uid='.$uid);
                    sendEmail($data['email'], "邮箱激活 - 信院小站", "<body style=\"margin:0;\">
<div style=\"width:100%;height:100%;min-height:600px;\">
    <img src=\"http://s.icodef.com/static/image/email_bg.jpg\" style=\"width:100%;height:100%;position: absolute;z-index: -1;\">
    <div style=\"color: rgba(255, 0, 0, 1);margin-left: 15%;width: 70%;padding:20px\">
        <div style=\"padding:10px;background: rgba(160, 160, 160, 0.5);border-radius: 5px;\">
                <h1>请点击下面的链接激活你的账号</h1>
                 <a href='{$url}'>{$url}</a>
        </div>
    </div>
</div>
</body>");
                    DB('send_email')->insert(['uid'=>$uid,'time'=>$code,'type'=>'1']);
                    //DB('inv_code')->update(['inv_use_uid' => $uid, 'inv_use_time' => time()], ['inv_code' => $_POST['inv_code']]);
                    DB('usergroup')->insert(['uid' => $uid, 'group_id' => 3]);
                }
            } else {
                $json['msg'] = $ret;
            }

            return json($json);
        } else {
            V()->assign('title', '注册页面');
            V()->display();
        }
    }

    public function email($code,$uid){
        $msg=DB('send_email')->find(['uid'=>$uid,'time'=>$code,'type'=>1]);
        if ($msg){
            DB('usergroup')->insert(['uid' => $uid, 'group_id' => config('base_auth')]);
            DB('send_email')->delete(['uid'=>$uid,'time'=>$code,'type'=>1]);
            header('Location:'.url('index/index/error','error=激活成功,即将登陆&url='.url('index/login/login')));
        }else{
            header('Location:'.url('index/index/error','error=激活失败&url='.url('index/login/login')));
        }
    }
}