<?php
/**
 *============================
 * author:Farmer
 * time:2017/7/25 12:56
 * blog:blog.icodef.com
 * function:影视提交
 *============================
 */

namespace app\user\ctrl;


use app\common\ctrl\auth;

class movie extends auth {
    public function index() {
        V()->assign('title', '影视提交');
        V()->display();
    }

    public function mlist($page = 1) {
        if ($page <= 0) {
            $page = 1;
        }
        $retJson = [];
        $rec = DB('video')->select(['status' => '1', 'father_vid' => -1, '__limit' => (($page - 1) * 20) . ',20'], 'count(*)');
        $count = $rec->fetch();
        $rec = DB('video')->select(['status' => '1', 'father_vid' => -1, '__order by' => 'vid desc,live desc', '__limit' => (($page - 1) * 20) . ',20'], 'vid,pay,name,image_url');
        $retJson = ['code' => 0, 'msg' => 'success'];
        $retJson['page_all'] = ceil($count['count(*)'] / 20);
        while ($tmp = $rec->fetch()) {
            $tmp['vid'] = floatval($tmp['vid']);
            $tmp['pay'] = floatval($tmp['pay']);
            $retJson['rows'][] = $tmp;
        }
        return json($retJson);
    }

    public function volume($vid = 0) {
        $retJson = ['code' => -1, 'msg' => 'error:not find'];
        if ($tmp = DB('video')->find(['vid' => $vid, 'father_vid' => -1], 'name,vid,url,mark,introduction,release_time,image_url,pay,type')) {
            $retJson = ['code' => 0, 'msg' => 'success'];
            $tmp['vid'] = floatval($tmp['vid']);
            $tmp['pay'] = floatval($tmp['pay']);
            $tmp['mark'] = floatval($tmp['mark']);
            $retJson['rows'] = $tmp;
            $rec = DB('video')->select(['status' => ['1', '>='], 'father_vid' => $vid], 'name,status,vid,url,pay,type');
            while ($tmp = $rec->fetch()) {
                $tmp['vid'] = floatval($tmp['vid']);
                $tmp['pay'] = floatval($tmp['pay']);
                $tmp['status'] = floatval($tmp['status']);
                $retJson['rows']['part'][] = $tmp;
            }
        }
        return json($retJson);
    }

    public function post($name = '') {
        $retJson = ['code' => 0, 'msg' => 'success'];
        DB('video')->insert(['name' => $name, 'uid' => $_COOKIE['uid'], 'time' => time()]);
        return json($retJson);
    }

    public function api() {
        $key = 'S7hA9PTrW5sPFQuL';//填写你的key
        $url = input('get.url');//解析视频的url
        $apiUrl = 'http://video.visha.cc/';//接口域名
        $domain = 'http://127.0.0.1/video';//你的域名
        $do = input('get.do');
        $v = input('get.v');
        header('Content-type:text/json');
        $apiUrl .= '?action=api&url=' . $url . '&key=' . $key . ($v !== '0' ? ('&v=' . $v) : '');
        echo file_get_contents($apiUrl);
    }
}