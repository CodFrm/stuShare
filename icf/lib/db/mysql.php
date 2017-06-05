<?php

/**
 *============================
 * author:Farmer
 * time:2017年1月4日 下午10:21:38
 * blog:blog.icodef.com
 * function:mysql数据库操作类
 *============================
 */
namespace icf\lib\db;
/**
 * 记录集类
 *
 * @author Farmer
 * @version 1.0
 */
class record {
	function __construct($result) {
		$this->result = $result;
	}
	protected $result;
	public function fetch() {
		return mysqli_fetch_array ( $this->result,MYSQLI_ASSOC );
	}
	/**
	* 获取记录集所有数据,以数组形式返回
	* @access public
	* @author Farmer
	* @return array
	*/
	public function fetchAll() {
        $rows=array();
        while ($row=mysqli_fetch_array($this->result,MYSQLI_ASSOC)){
            $rows[]=$row;
        }
        return $rows;
	}
	/**
	 * 返回上次查询得到的数据条数
	 * 如果想获取全部的,在$field中加入sql_calc_found_rows
	 * @access public
	 * @author Farmer
	 * @return int
	 */
	public function count() {
		return mysqli_num_rows($this->result);
	}
	/**
	* 返回上次查询全部数据条数
	* 如果想获取全部的,在$field中加入sql_calc_found_rows
	* @access public
	* @author Farmer
	* @return int
	*/
	public function countAll() {
		$rec=DB('t')->query('select FOUND_ROWS()');
		return $rec->fetch()['FOUND_ROWS()'];
	}
}
class mysql {
	protected $con;
	function __construct($server,$user,$pwd,$db) {
		$this->con = mysqli_connect ( $server, $user, $pwd );
		if (! $this->con) {
			die ( 'Could not connect: ' . mysql_error () );
			return;
		}
		$this->exec ( 'set names utf8' );
		mysqli_select_db ( $this->con, $db );
	}
	function __destruct() {
		mysqli_close($this->con);
	}
	/**
	 * 返回上一条插入id
	 *
	 * @author Farmer
	 * @param null $null
	 * @return int
	 */
	public function lastinsertid() {
		return mysqli_insert_id ( $this->con );
	}
	/**
	 * 获取错误信息
	 *
	 * @author Farmer
	 * @param NULL $null
	 * @return array
	 */
	public function getError() {
		return mysqli_error ( $this->con );
	}
	/**
	 * 执行一条sql语句
	 *
	 * @author Farmer
	 * @param string $sql	
	 * @return int
	 */
	public function exec($sql) {
		mysqli_query ( $this->con, $sql );
		return mysqli_affected_rows ( $this->con );
	}
	/**
	 * 执行一条sql语句查询,返回查询结果
	 *
	 * @author Farmer
	 * @param string $sql        	
	 * @return \icf\lib\db\record
	 */
	public function query($sql) {
		$sql = new record ( mysqli_query ( $this->con, $sql ) );
		return $sql;
	}

}