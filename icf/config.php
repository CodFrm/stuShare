<?php

/**
 *============================
 * author:Farmer
 * time:2017年1月4日 9:22:01
 * blog:blog.icodef.com
 * function:配置文件
 *============================
 */
return array(
    // 定义数据库信息
    'DB_USER' => 'root',
    'DB_PWD' => '',
    'DB_DATABASE' => 'stu',
    'DB_SERVER' => 'localhost',
    'DB_PREFIX' => 'share_',
    // 数据库引擎
    '__DB_' => 'mysql',
    // 调试模式
    '__DEBUG_' => true,
    // 模板后缀
    'TPL_SUFFIX' => 'html',
    // 默认操作变量
    'ACTION' => 'get.action',
    //默认控制器变量
    'CTRL' => 'get.ctrl',
    // 路由规则
    'ROUTE_RULE' => [
        '{s}.php' => '${1}->index->index',
        '{s}/{s}' => '${1}->${2}',
        '{s}' => '${1}->index',
    ],
    'PUBLIC' => 'public',
);

