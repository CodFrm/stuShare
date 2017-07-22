<?php

/**
 *============================
 * author:Farmer
 * time:下午7:44:09
 * blog:blog.icodef.com
 * function:视图类
 *============================
 */
namespace icf\lib;

/**
 * 视图类
 *
 * @author Farmer
 * @version 1.0
 */
class view {
	private $tplValues = array ();

	/**
	 * 设置值
	 *
	 * @author Farmer
	 * @param string $param
	 * @param string $value
	 * @return
	 *
	 */
	public function assign($param, $value) {
		$this->tplValues [$param] = $value;
	}
	/**
	 * 载入编译模板文件
	 *
	 * @author Farmer
	 * @param string $filename
	 * @return bool
	 */
	public function display($filename='') {
	    if($filename===''){
	        $filename=input('action');
        }
		if(strpos($filename,'/')==FALSE ){
			$path = __ROOT_ . '/app/' . input('model') . '/tpl/'.input('ctrl').'/' . $filename;
		}else{
			$path = __ROOT_ . '/app/' .  input('model')  . '/tpl/'. $filename;
		}
		$suffix='.'.input('config.TPL_SUFFIX');
		if(substr($path,strlen($path)-strlen($suffix),strlen($suffix))!=$suffix){
		    $path.=$suffix;
        }
		if (! file_exists ( $path )) {
			echo '</br>load error';
			return false;
		}
		$cache = __ROOT_ . '/app/cache/tpl/' . md5 ( $path ) . '.php';
		$this->fetch ( $path, $cache );
		return $this;
	}
	/**
	 * 生成编译文件并输出
	 *
	 * @author Farmer
	 * @param string $path
	 * @param string $cache
	 * @return
	 *
	 */
	private function fetch($path, $cache) {
		$fileData = file_get_contents ( $path );
		if ( !file_exists ( $cache ) || filemtime ( $path ) > filemtime ( $cache )) {
			$pattern = array (
					'/\{(\$[a-zA-Z0-9\[\]\']+)\}/', // 匹配{$xxx}
					'/{while (.*?)}/',
					'/{\$(.*?) = (.*?)}/',
					'/{\/while}/',
					'/{break}/',
					'/{continue}/',
					'/{if (.*?)}/',
					'/{\/if}/',
					'/{elseif (.*?)}/',
					'/{else}/' ,
					'/{foreach (.*?)}/',
					'/{\/foreach}/',
					"/{include '(.*?)'}/",
					'/{(\$[a-zA-Z0-9_\[\]\']*[+-]*)\}/',
					'/{\:([^"^\r^}]+)}/'
			);
			$replace = array (
					'<?php echo ${1};?>',
					'<?php while(${1}):?>',
					'<?php \$${1}=${2};?>',
					'<?php endwhile;?>',
					'<?php break;?>',
					'<?php continue;?>',
					'<?php if(${1}):?>',
					'<?php endif;?>',
					'<?php elseif(${1}):?>',
					'<?php else:?>' ,
					'<?php foreach(${1}):?>',
					'<?php endforeach;?>',
					'<?php V()->display("${1}");?>',
					'<?php ${1};?>',
					'<?php echo ${1};?>'
			);
			$cacheData = preg_replace ( $pattern, $replace, $fileData );
			file_put_contents ( $cache, $cacheData );
		} else {
			$cacheData = file_get_contents ( $cache );
		}
		$pattern = array (
				'/__PUBLIC__/',
				'/__HOME__/'
		);
		$replace = array (
				input('config.PUBLIC'),
				__HOME_
		);
		$cacheData = preg_replace ( $pattern, $replace, $cacheData );
		preg_match_all ( '/\{\$([a-zA-Z0-9]+)\}/', $fileData, $tmp );
		for($i = 0; $i < sizeof ( $tmp [1] ); $i ++) {
			if (! isset ( $this->tplValues [$tmp [1] [$i]] )) {
				$this->tplValues [$tmp [1] [$i]] = '';
			}
		}
		extract ( $this->tplValues );
		eval ( '?>' . $cacheData );
	}
}