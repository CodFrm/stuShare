<?php
/**
 *============================
 * author:Farmer
 * time:2017/8/22 12:44
 * blog:blog.icodef.com
 * function:
 *============================
 */

namespace app\admin\ctrl;


use app\common\ctrl\auth;

class server extends auth {
    public function index() {
        $data = DB('server')->select()->fetchAll();
        V()->assign('server_list', $data);
        V()->display();
    }

    public function edit($svid = 0) {
        $data = ['name'=>'','ip'=>'','config'=>'','svid'=>0];
        if ($svid > 0) {
            $data = DB('server')->find(['svid' => $svid]);
        }
        V()->assign('smsg', $data);
        V()->display();
    }

    public function post($svid = 0) {
        $retJson = ['code' => -1, 'msg' => '错误,svid不存在'];
        $ret = isExist($_POST, [
            'name' => ['msg' => '请输入主机名', 'sql' => 'name'],
            'config' => ['msg' => '请输入配置文件', 'sql' => 'config'],
            'ip' => ['msg' => '请输入服务器ip', 'sql' => 'ip']], $sql);
        if ($ret === true) {
            if ($svid > 0) {
                DB('server')->update($sql, ['svid' => $svid]);
                $ret = '修改成功';

            } else {
                DB('server')->insert($sql, ['svid' => $svid]);
                $ret = '增加成功';
            }
            $retJson['code'] = 1;
        }
        $retJson['msg'] = $ret;
        return json($retJson);
    }

    public function delete($svid) {
        DB('server')->delete(['svid' => $svid]);
        $retJson = ['code' => 0, 'msg' => '删除成功'];
        header('Location: ' . $_SERVER['HTTP_REFERER'] ?: url('admin/server/index'));
        return json($retJson);
    }
}