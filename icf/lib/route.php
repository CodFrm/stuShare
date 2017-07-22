<?php

/**
 *============================
 * author:Farmer
 * time:2017年1月21日 上午11:55:34
 * blog:blog.icodef.com
 * function:路由类
 *============================
 */
namespace icf\lib;

/**
 * 路由类
 *
 * @author Farmer
 * @version 1.0
 */
class route {
    // 路由规则
    private static $routeRule = array();
    // 替换规则
    private static $replaceRule = array(
        '{n}' => '(\d+)',
        '{s}' => '([\S][^\/]*)',
        '/' => '\/'
    );

    /**
     * 替换规则处理
     *
     * @access private
     * @author Farmer
     * @param string $str
     * @return string
     */
    static private function replaceHandle($str) {
        if (strrpos($str, '}')) {
            $str = preg_replace('/{[\S][^\{^\}]*}/', '{s}', $str);
            $str = strtr($str, route::$replaceRule);
            $str = '/\/' . $str . '/';
        } else {
            $str = '/^\/' . $str . '$/';
        }
        return $str;
    }

    /**
     * 添加一条或多条路由规则
     *
     * @access public
     * @author Farmer
     * @param mixed $rule
     * @param string $to
     * @return null
     */
    static function add($rule, $to = 0) {
        if (is_array($rule)) {
            foreach ($rule as $pattern => $value) {
                route::$routeRule [$pattern] = $value;
            }
        } else {
            route::$routeRule [$rule] = $to;
        }
    }

    /**
     * 加载控制器操作
     *
     * @access private
     * @author Farmer
     * @param string $ctrl
     * @param string $action
     * @param string $pattern
     * @param string $oldPattern
     * @return bool
     */
    static private function loadCtrlAction($model, $ctrl, $action, $pattern = 0, $oldPattern = 0) {
        $className = __APP_ . '/' . $model . '/ctrl/' . $ctrl;
        if (file_exists($className . '.php')) {
            $className = preg_replace('/\//', '\\', $className);
            $comPath=__APP_.'/common.php';
            if (file_exists($comPath )) {
                require_once $comPath;
            }
            G('model', $model);
            G('ctrl', $ctrl);
            G('action', $action);
            $comPath = __APP_ . '/' . $model . '/';
            if (file_exists($comPath . 'common.php')) {
                require_once $comPath . 'common.php';
            }
            $Object = new $className ();
            if (method_exists($Object, $action)) {
                if ($pattern) {
                    preg_match_all($pattern, '/' . preg_replace([
                            '/{/',
                            '/}/'
                        ], '', $oldPattern), $key);
                    preg_match_all($pattern, $_SERVER ['PATH_INFO'], $value);
                    unset ($key [0]);
                    unset ($value [0]);
                    foreach ($key as $k => $v) {
                        $_GET [$v [0]] = $value [$k] [0];
                    }
                    G('get', $_GET);
                }
                if (file_exists(__APP_ . '/' . $model . '/config.php')) {
                    $config = array_merge_recursive(input('config'), include __APP_ . '/' . $model . '/config.php');
                    G('config', $config);
                }
                // 获取方法参数
                $method = new \ReflectionMethod ($Object, $action);
                // 参数绑定
                $param = array();
                foreach ($method->getParameters() as $value) {
                    if (input('get.' . $value->getName())!==false) {
                        $param [] = input('get.' . $value->getName());
                    } else {
                        $param [] = $value->getDefaultValue();
                    }
                }
                if(method_exists($Object, '__init')) {
                    call_user_func_array(array($Object, '__init'));
                }
                echo call_user_func_array(array(
                    $Object,
                    $action
                ), $param);
                return true;
            }
        }
        return false;
    }

    /**
     * 解析路由
     *
     * @access public
     * @author Farmer
     * @return null
     */
    static function analyze() {
        if (isset ($_SERVER ['PATH_INFO'])) {
            $isSuccess = false;
            foreach (route::$routeRule as $pattern => $replace) {
                $oldPattern = $pattern;
                $pattern = route::replaceHandle($pattern);
                $arr = route::routeHndle($_SERVER ['PATH_INFO'], [
                    $pattern,
                    $replace
                ]);
                $model = $arr [0];
                $ctrl = $arr [1];
                $action = $arr [2];
                if ($ctrl) {
                    if (route::loadCtrlAction($model, $ctrl, $action, $pattern, $oldPattern)) {
                        $isSuccess = true;
                        break;
                    }
                }
            }
            if (!$isSuccess) {
                echo '404';
            }
        } else {
            $ctrl = input(input('config.CTRL')) ?: 'index';
            $action = input(input('config.ACTION')) ?: 'index';
            if (!route::loadCtrlAction(__MODEL_, $ctrl, $action)) {
                echo '404';
            }
        }
    }

    /**
     * 路由规则处理
     *
     * @access private
     * @author Farmer
     * @return array
     */
    static private function routeHndle($path, $match) {
        $cacheData = preg_replace($match [0], $match [1], $path, -1, $n);
        if ($n <= 0) {
            return [
                0,
                0,
                0
            ];
        }
        $ctrl = 0;
        $action = 0;
        $model = 0;
        if (strrpos($cacheData, '->')) {
            preg_match_all('/([\d\w]+)/', $cacheData, $tmp);
            if (sizeof($tmp [0]) == 2) {
                $ctrl = $tmp [0] [0];
                $action = $tmp [0] [1];
            } else if (sizeof($tmp [0]) == 3) {
                $model = $tmp [0] [0];
                $ctrl = $tmp [0] [1];
                $action = $tmp [0] [2];
            }
        }
        return [
            $model ?: __MODEL_,
            $ctrl ?: $cacheData,
            $action ?: (input(input('config.ACTION'))) ?: 'index'
        ];
    }
}

