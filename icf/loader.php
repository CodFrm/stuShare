<?php
/**
 *============================
 * author:Farmer
 * time:下午7:26:45
 * blog:blog.icodef.com
 * function:自动加载类
 *============================
 */
spl_autoload_register ( 'Loader::loadClass' );
class loader {
	static $fileArray=array();
	static $system=[__APP_,'icf'];
	static function loadClass($ClassName) {
		if (!isset(loader::$fileArray [$ClassName])) {
			if(!in_array(substr($ClassName,0,3),loader::$system)){
				$ClassName='icf\\lib\\'.$ClassName;
			}
			loader::$fileArray[$ClassName]=1;
			$ClassName=str_replace('\\','/',$ClassName);
			if(file_exists(__ROOT_.'/'.$ClassName . '.php')){
				require_once __ROOT_.'/'.$ClassName . '.php';
			}
		}
	}
}