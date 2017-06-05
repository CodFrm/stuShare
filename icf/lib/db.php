<?php

/**
 *============================
 * author:Farmer
 * time:2017年1月4日 下午8:46:47
 * blog:blog.icodef.com
 * function:数据库操作类
 *============================
 */
namespace icf\lib;

/**
 * 数据库操作类
 *
 * @author Farmer
 * @version 1.0
 */
class db {
	// 数据库操作对象
	static $db;
	// 操作的表
	private $table;
	//解析出来的表
	private  $arrTable=array();
	// 逻辑运算符
	private $logical = array (
			'or',
			'and'
	);
	static function init() {
		if (input('config.__DB_') == 'mysql') {
			db::$db= new \icf\lib\db\mysql ( input('config.DB_SERVER'), input('config.DB_USER'), input('config.DB_PWD'), input('config.DB_DATABASE') );
		}
	}
	public function getDBObject($table = '') {
	    if(substr($table,0,1)==':'){
            $this->table = substr($table,1);
        }else {
            $this->table = input('config.DB_PREFIX') . str_replace('|', ',' . input('config.DB_PREFIX'), $table);
            preg_match_all('/,([\d\w_]+)/', ',' . $this->table, $arr);//直接在前面加个逗号...不处理了...
            $this->arrTable = $arr[1];
        }
		return $this;
	}
	public function __call($func, $arguments) {
		if (is_null ( db::$db )) {
			return 0;
		}
		return call_user_func_array ( array (
				db::$db,
				$func 
		), $arguments );
	}
	/**
	 * SQL数据处理
	 *
	 * @access public
	 * @author Farmer
	 * @param string $sql        	
	 * @return string
	 */
	public function sqlHandle($str) {
		return addslashes ( $str );
	}
	/**
	 * 类型转换
	 *
	 * @access protected
	 * @author thinkphp
	 * @param mixed $value      	
	 * @return mixed
	 */
	protected function parseValue($value) {
		if (is_string ( $value )) {
			$value = '\'' . $this->sqlHandle ( $value ) . '\'';
		} elseif (isset ( $value [0] ) && is_string ( $value [0] ) && strtolower ( $value [0] ) == 'exp') {
			$value = $this->sqlHandle ( $value [1] );
		} elseif (is_array ( $value )) {
			$value = array_map ( array (
					$this,
					'parseValue' 
			), $value );
		} elseif (is_bool ( $value )) {
			$value = $value ? '1' : 'null';
		} elseif (is_null ( $value )) {
			$value = 'null';
		}
		return $value;
	}
	/**
	 * 数组转换成查询的条件
	 *
	 * @access protected
	 * @author Farmer
	 * @param array $where
	 * @return string
	 */
	protected function where($where) {
		$sql = ' ';
		$logical='';
		$subscript=0;
		foreach ( $where as $key => $value ) {
			if (is_array ( $value )) {
				$arrsize = sizeof ( $value );
				$isarr = is_array ( $value [0] );
				$operator='';
				$value[0]=($isarr ? '(' : '') . implode ( ',', $this->parseValue ( $isarr ? $value [0] : array (
						$value [0] 
				) ) ) . ($isarr ? ')' : '');
				$logical='';
				for($n = 1; $n < $arrsize; $n ++) {
					$value[$n]=strtolower($value[$n]);
					if(in_array($value[$n],$this->logical)){
						$logical=$value[$n];
					}else{
						$operator=$value[$n];
					}
				}
                $sql.=($subscript++==0?'where ':($logical?:'and'));
				if($operator=='' and $isarr){
					$operator='in';
				}
				$sql .= preg_replace ( array (
						'/\$key/',
						'/\$operator/',
						'/\$value/'
				), array (
						$key,
						$operator?:'=',
						$value[0]?:'null',
				), ' $key $operator $value ' );
			}else if(is_numeric($key)){
				$sql.=($subscript++==0?'where ':' and ').$value.' ';
			} else if(substr($key,0,2)=='__'){
				$sql.=' '.substr($key,2).' '.$value.' ';
			}else{
				$sql.=($subscript++==0?'where ':' and ').$key.' = ' .$this->parseValue ( $value);
			}
		}
		return $sql;
	}
	/**
	 * 插入一条数据返回改变条数
	 * 
	 * @access public
	 * @author Farmer
	 * @param array $data        	
	 * @return int
	 */
	public function insert($data = 0) {
		if (! empty ( $data )) {
			$table = $this->table;
			$sql = 'insert into ' . $table . '(`' . implode ( '`,`', array_keys ( $data ) ) . '`)values(' . implode ( ',', $this->parseValue ( $data ) ) . ');';
			return $this->exec ( $sql );
		}
		return 0;
	}
	/**
	 * 删除数据返回变化条数
	 *
	 * @author Farmer
	 * @param mixed $where        	
	 * @return int
	 */
	public function delete($where=0) {
		$sql="delete from $this->table" ;
		if (is_string ( $where )) {
			$sql .=$where;
		} else if (is_array ( $where ) and !empty($where)) {
			$sql .= $this->where ( $where );
		}
		return $this->exec($sql);
	}
	/**
	 * 修改数据返回变化条数
	 *
	 * @access public
	 * @author Farmer
	 * @param array $where        	
	 * @return int
	 */
	function update($data,$where=0) {
		$sql = "update $this->table set ";
		if(is_string($data)){
			$sql.=$data;
		}else if (is_array($data)){
			$add=0;
			foreach ($data as $key=>$value) {
				$sql.=($add++!=0?',':'').$key.'='.$this->parseValue($value);
			}
		}
	 	if (is_string ( $where )) {
			$sql .= $where;
		}else if (is_array ( $where ) ) {
			$sql .= $this->where ( $where );
		}
		return $this->exec ( $sql );
	}

    /**
     * 查询返回sql记录集
     * @author Farmer
     * @param int $where
     * @param int $field
     * @param string $join
     * @return mixed
     */
	function select($where=0,$field=0,$join='') { // 我竟然写得这么麻烦....迟早有一天我要改掉 我来改啦 2017/1/5 时隔一年?233
		$sql='select '.(empty($field)?'*':$field);
		$sql.= " from $this->table $join";
		if (is_string ( $where )) {
			$sql .= $where;
		}else if (is_array ( $where ) ) {
            $sql .= $this->where($where);
        }
		return $this->query ( $sql );
	}

    /**
     * 查找
     * @author Farmer
     * @param int $where
     * @param int $field
     * @param string $join
     * @return mixed
     */
    function find($where=0,$field=0,$join=''){
        $where['__limit']='1';
        return $this->select($where,$field,$join)->fetch();
    }
    /**
     * 开始事务
     * @author Farmer
     */
	function begin(){
	    $this->exec('begin');
    }

    /**
     * 结束事务
     * @author Farmer
     */
    function end(){
        $this->exec('end');
    }

    /**
     * 提交事务
     * @author Farmer
     */
    function commit(){
        $this->exec('commit');
        $this->exec('end');
    }

    /**
     * 回滚事务
     * @author Farmer
     */
    function rollback(){
        $this->exec('rollback');
        $this->exec('end');
    }
}