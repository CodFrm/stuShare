<?php
/**
 *============================
 * author:Farmer
 * time:2017年1月4日 下午7:23:57
 * blog:blog.icodef.com
 * function:入口文件
 *============================
 */
header('Content-type: text/html; charset=utf-8');
error_reporting(E_ALL);
define('__ROOT_',__DIR__);
define('__MODEL_','index');
define('__APP_','app');
require_once __ROOT_.'/icf/loader.php';
__ROOT_.'\\'.icf\index::Init();

