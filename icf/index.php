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
if(file_exists(__APP_ . '/' . __MODEL_ . '/config.php')){
	$config=array_merge_recursive( include 'config.php',include __APP_ . '/' . __MODEL_ . '/config.php' );
}else{
	$config=include 'config.php';
}
G('config',$config);
if (input('config.__DEBUG_')) {
	error_reporting ( E_ALL );
	ini_set ( 'display_errors', '1' );
} else {
	error_reporting ( E_ALL ^ E_NOTICE ^ E_WARNING );
}
if(isset($_SERVER['PATH_INFO'])){
	if(!empty($_SERVER['QUERY_STRING'])){
		$home=str_replace($_SERVER['PATH_INFO'],'',substr($_SERVER['REQUEST_URI'],0,strrpos($_SERVER['REQUEST_URI'],'?')));
	}else{
		$home=str_replace($_SERVER['PATH_INFO'],'',$_SERVER['REQUEST_URI']);
	}
}else{
	$home=substr ($_SERVER['REQUEST_URI'],0,strrpos($_SERVER['REQUEST_URI'],'/'));
}
define('__HOME_',$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$home);
class index {
	static function init() {
		G('get',$_GET);
		G('post',$_POST);
		G('cookie',$_COOKIE);
		G('server',$_SERVER);
		M();
		lib\route::add(input('config.ROUTE_RULE'));
		lib\route::analyze();
	}
}
