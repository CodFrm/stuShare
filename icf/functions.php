<?php

/**
 *============================
 * author:Farmer
 * time:2017年1月4日 下午7:23:25
 * blog:blog.icodef.com
 * function:框架系统函数
 *============================
 */

/**
 * Json 编码 对于中文处理 仅支持php5.4以后的版本
 *
 * @author Farmer
 * @param string $str
 * @return string
 */
function json($str) {
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    header('Content-Type: application/json; charset=utf-8');
    return json_encode ( $str, JSON_UNESCAPED_UNICODE );
}

/**
 * 判断变量是否设置
 * @author Farmer
 * @param $array
 * @param $mode
 * @return bool
 */
function isExist($array, $mode,&$data='') {
    foreach ($mode as $key => $value) {
        if (is_string($value)) {
            if (empty($array[$key])) {
                return $value;
            }
        } else if (is_array($value)) {
            if (empty($array[$key])) {
                return $value['msg'];
            }
            if (!empty($value['regex'])) {//正则
                if (!preg_match($value['regex'][0], $array[$key])) {
                    return $value['regex'][1];
                }
            }
            if (!empty($value['function'])) {//对函数处理
                $tmpFunction=$value['function'];
                $funName=$value['function'][0];
                $parameter=array();
                unset($tmpFunction[0]);
                $parameter[]=$array[$key];
                foreach ($tmpFunction as $v){
                    $parameter[]=$array[$v];
                }
                $tmpValue = call_user_func_array($funName,$parameter);
                if ($tmpValue !== true) {
                    return $tmpValue;
                }
            }
            if(!empty($value['enum'])){//判断枚举类型
                if(!in_array($array[$key],$value['enum'][0])){
                    return $value['enum'][1];
                }
            }
            if(!empty($value['sql'])){//将其复制给sql插入数组
                $data[$value['sql']]=$array[$key];
            }
        }
    }
    return true;
}

$_model = array();
/**
 * 获取数据库对象
 *
 * @author Farmer
 * @param string $table
 * @return \icf\lib\db()
 */
function DB($table = '') {
    $db = new \icf\lib\db();
    if (!empty ($table)) {
        return $db->getDBObject($table);
    }
    return $db;
}

/**
 * 实例化一个没有模型文件的Model
 *
 * @author Farmer
 * @param string $table
 * @return \icf\lib\model()
 */
function M() {
    if (!G('model')) {
        G('model', new \icf\lib\model ());
    }
    return G('model');
}

/**
 * 实例化一个模板引擎view
 *
 * @author Farmer
 * @return \icf\lib\view()
 */
function V() {
    if (!G('view')) {
        G('view', new \icf\lib\view ());
    }
    return G('view');;
}

/**
 * 获取或者设置一个全局变量
 *
 * @author Farmer
 * @param string $var
 * @param var $val
 * @return mixed
 */
function G($var, $val = 0) {
    static $_globals = array();
    if ($val === 0) {
        if (!isset ($_globals [$var])) {
            return false;
        }
        return $_globals [$var];
    }
    $_globals [$var] = $val;
}

/**
 * 获取变量
 *
 * @author Farmer
 * @param string $var
 * @return mixed
 */
function input($var) {
    $arrVar = explode('.', $var);
    if (sizeof($arrVar) <= 1) {
        $ret = G($var);
    } else {
        $ret = G($arrVar [0]);
        unset ($arrVar [0]);
        foreach ($arrVar as $value) {
            if (!isset ($ret [$value])) {
                return false;
            }
            $ret = $ret [$value];
        }
    }
    return $ret;
}

/**
 * 输出一行信息
 *
 * @author Farmer
 * @param string $var
 * @param var $val
 * @return var
 */
function outmsg($msg) {
    if (is_string($msg)) {
        echo '<pre style="background-color:#CCC;color:#06F;border-bottom:1px solid #999;border-top:1px solid #999;border-right:1px solid #999;border-left:1px solid #999;border-radius:4px;font-size:18px;vertical-align: middle; word-wrap:break-word; word-break:normal; ">string:' . $msg . '</pre>';
    } else if (is_array($msg)) {
        echo '<pre style="background-color:#CCC;color:#06F;border-bottom:1px solid #999;border-top:1px solid #999;border-right:1px solid #999;border-left:1px solid #999;border-radius:4px;font-size:18px;vertical-align: middle; word-wrap:break-word; word-break:normal; ">';
        print_r($msg);
        echo '</pre>';
    }
}


///**
// * 生成访问URL
// * @author Farmer
// * @param string $action
// * @param string $param
// * @return string
// */
//function url($action='',$param='') {
//    preg_match_all( '/([\w]+)/', $action, $arrMatch);
//    $url='/';
//    if(sizeof($arrMatch)==1){
//        $url.='action='.$arrMatch[0][0];
//    }else if(sizeof($arrMatch[0])==2){
//        $url.='?ctrl='.$arrMatch[0][2].'&action='.$arrMatch[0][1];
//    }else if(sizeof($arrMatch[0])==3){
//        $url.=$arrMatch[0][0].'.php?ctrl='.$arrMatch[0][1].'&action='.$arrMatch[0][2];
//    }
//
//    return __HOME_.$url.($param?('&'.$param):'');
//}
/**
 * 生成访问URL
 * @author Farmer
 * @param string $action
 * @param string $param
 * @return string
 */
function url($action='',$param='') {
    preg_match_all( '/([\w]+)/', $action, $arrMatch);
    $url='';
    foreach ($arrMatch[0] as $value){
        $url.=('/'.$value);
    }
    return __HOME_.$url.($param?('?'.$param):'');
}