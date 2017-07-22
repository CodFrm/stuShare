<?php

/**
 *============================
 * author:Farmer
 * time:下午7:30:59
 * blog:blog.icodef.com
 * function:核心处理文件
 *============================
 */

namespace icf;
include 'functions.php';
date_default_timezone_set('PRC');
if (file_exists(__APP_ . '/' . __MODEL_ . '/config.php')) {
    $config = array_merge_recursive(include 'config.php', include __APP_ . '/' . __MODEL_ . '/config.php');
} else {
    $config = include 'config.php';
}
G('config', $config);
if (input('config.__DEBUG_')) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
}
$home = $_SERVER['REQUEST_URI'];
if (!empty($_SERVER['QUERY_STRING'])) {
    $home=substr($_SERVER['REQUEST_URI'],0,strpos($home,'?'));
}
if (isset($_SERVER['PATH_INFO'])) {
    $home = str_replace($_SERVER['PATH_INFO'], '', $home);
} else {
    $home = substr($home, 0, strrpos($home, '/'));
}
if (!isset($_SERVER['REQUEST_SCHEME'])) {
    $_SERVER['REQUEST_SCHEME'] = 'http';
}
define('__HOME_', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $home);
class index {
    static function init() {
        G('get', $_GET);
        G('post', $_POST);
        G('cookie', $_COOKIE);
        G('server', $_SERVER);
        M();
        lib\route::add(input('config.ROUTE_RULE'));
        lib\route::analyze();
    }
}
