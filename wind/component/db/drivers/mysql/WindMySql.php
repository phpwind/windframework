<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-11
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import ( 'WIND:component.db.base.WindDbAdapter' );
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindMySql extends WindDbAdapter {
	/* (non-PHPdoc)
	 * @see wind/base/WDbAdapter#connect()
	 */
	protected function connect() {
		if (!is_resource ( $this->connection )) {
			$this->connection = $this->config ['pconnect'] ? mysql_pconnect ( $this->config ['host'], $this->config ['dbuser'], $this->config ['dbpass'] ) : mysql_connect ( $this->config ['host'], $this->config ['dbuser'], $this->config ['dbpass'], $this->config ['force'] );
			$this->changeDB ( $this->config ['dbname'] );
			$this->setCharSet ( $this->config ['charset'] );
		}
		return $this->connection;
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WDbAdapter#query()
	 */
	public function query($sql) {
		$this->query = mysql_query ( $sql, $this->connection );
		$this->error ($sql);
		return true;
	}
	
	public function getAffectedRows(){
		return mysql_affected_rows($this->connection);
	}
	
	public function getLastInsertId(){
		return mysql_insert_id($this->connection);
	}
	
	public function getMetaTables($schema = ''){
		$schema = $schema ? $schema : $this->getSchema();
		if(empty($schema)){
			throw new WindSqlException (WindSqlException::DB_EMPTY);
		}
		$this->query('SHOW TABLES FROM '.$schema);
		return $this->getAllRow(MYSQL_ASSOC);
	}
	
	public function getMetaColumns($table){
		if(empty($table)){
			throw new WindSqlException (WindSqlException::DB_TABLE_EMPTY);
		}
		$this->query('SHOW COLUMNS FROM '.$table);
		return $this->getAllRow(MYSQL_ASSOC);
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WDbAdapter#getAllRow()
	 */
	public function getAllRow($fetch_type) {
		if (! is_resource ( $this->query )) {
			throw new WindSqlException ( WindSqlException::DB_QUERY_LINK_EMPTY );
		}
		if (! in_array ( $fetch_type, array (1, 2, 3 ) )) {
			throw new WindSqlException ( WindSqlException::DB_QUERY_FETCH_ERROR );
		}
		$result = array ();
		while ( ($record = mysql_fetch_array ( $this->query, $fetch_type )) ) {
			$result [] = $record;
		}
		return $result;
	}
	
	public function getRow($fetch_type){
		return mysql_fetch_array ( $this->query, $fetch_type );
	}
	
	public function beginTrans() {
		if ($this->transCounter == 0) {
			$this->query ( 'START TRANSACTION');
		} elseif ($this->transCounter && $this->enableSavePoint) {
			$savepoint = 'savepoint_' . $this->transCounter;
			$this->query ( "SAVEPOINT `{$savepoint}`");
			array_push ( $this->savepoint, $savepoint );
		}
		++ $this->transCounter;
		return true;
	}
	
	public function commitTrans() {
		if ($this->transCounter <= 0) {
			throw new WindSqlException ( WindSqlException::DB_QUERY_TRAN_BEGIN );
		}
		-- $this->transCounter;
		if ($this->transCounter == 0) {
			if ($this->last_errstr) {
				$this->query ( 'ROLLBACK');
			} else {
				$this->query ( 'COMMIT');
			}
		} elseif ($this->enableSavePoint) {
			$savepoint = array_pop ( $this->savepoint );
			if ($this->last_errstr) {
				$this->query ( "ROLLBACK TO SAVEPOINT `{$savepoint}`" );
			}
		}
	}
	/* (non-PHPdoc)
	 * @see wind/base/WDbAdapter#close()
	 */
	public function close() {
		if($this->connection){
			mysql_close ( $this->connection );
		}
	}
	/* (non-PHPdoc)
	 * @see wind/base/WDbAdapter#dispose()
	 */
	public function dispose() {
		$this->close($this->connection);
		$this->connection = null;
		$this->query = null;
	}
	/**
	 * 取得mysql版本号
	 * @param string|int|resource $key 数据库连接标识
	 * @return string
	 */
	public function getVersion() {
		return mysql_get_server_info ( $this->connection);
	}
	/**
	 * @param string $charset 字符集
	 * @param string | int $key 数据库连接标识
	 * @return boolean
	 */
	public function setCharSet($charset) {
		$version = ( int ) substr ( $this->getVersion (), 0, 1 );
		if ($version > 4) {
			$this->query ( "SET NAMES '" . $charset . "'");
		}
		return true;
	}
	
	/**
	 * 切换数据库
	 * @see wind/base/WDbAdapter#changeDB()
	 * @param string $databse 要切换的数据库
	 * @param string|int|resource $key 数据库连接标识
	 * @return boolean
	 */
	public function changeDB($database) {
		return mysql_select_db($database,$this->connection);
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WDbAdapter#error()
	 */
	protected function error($sql) {
		$this->last_errstr = mysql_error ();
		$this->last_errcode = mysql_errno ();
		$this->last_sql = $sql;
		if ($this->last_errstr || $this->last_errcode) {
			throw new WindSqlException ( $this->last_errstr, $this->last_errcode );
		}
		return true;
	}
		
}