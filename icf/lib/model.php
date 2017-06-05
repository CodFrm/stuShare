<?php

/**
 *============================
 * author:Farmer
 * time:2017年1月4日 下午8:14:37
 * blog:blog.icodef.com
 * function:模型类
 *============================
 */
namespace icf\lib;

/**
 * 模型类
 *
 * @author Farmer
 * @version 1.0
 */
class model {
	private $db=null;
	private $con=0;
	public function __construct(){
		\icf\lib\db::init();
	}
	public function __call($func, $arguments){
		if(is_null($this->db)){
			return 0;
		}
		return call_user_func_array ( array (
						$this->db,
						$func 
				) ,$arguments);
	}
}