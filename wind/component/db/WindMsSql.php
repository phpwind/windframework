<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-18
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:component.db.base.WindDbAdapter');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindMsSql extends WindDbAdapter {
	public function connect($config, $key) {
		$this->key = $key;
		if (! ($this->linked[$key] = $this->getLinked ( $key ))) {
			$this->linked[$key] =  $config['pconnect'] ? mssql_pconnect ( $config['host'], $config ['dbuser'], $config ['dbpass'] ) : mssql_connect ( $config['host'], $config ['dbuser'], $config ['dbpass'], $config['force'] );
			if ($config ['dbname'] && is_resource ($this->linked[$key])) {
				$this->changeDB ( $config ['dbname'], $key);
			}
			if (!isset ( $this->config [$key] )) {
				$this->config [$key] = $config;
			}
		}
		return $this->linked[$key];
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WDbAdapter#query()
	 */
	public function query($sql, $key = '', $optype = '') {
		$this->getExecDbLink ( $optype, $key );
		$this->query = mssql_query ( $sql, $this->linked [$this->key] );
		$this->last_sql = $sql;
		$this->error ();
		$this->logSql ();
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WDbAdapter#getAllResult()
	 */
	public function getAllResult($fetch_type = MYSQL_ASSOC) {
		if (! is_resource ( $this->query )) {
			throw new WindSqlException (WindSqlException::DB_QUERY_LINK_EMPTY);
		}
		if (! in_array ( $fetch_type, array (1, 2, 3 ) )) {
			throw new WindSqlException (WindSqlException::DB_QUERY_FETCH_ERROR);
		}
		$result = array ();
		while ( ($record = mssql_fetch_array ( $this->query, $fetch_type )) ) {
			$result [] = $record;
		}
		return $result;
	}

	
	public function beginTrans($key = '') {

	}
	
	public function commitTrans($key = '') {

	}
	/* (non-PHPdoc)
	 * @see wind/base/WDbAdapter#close()
	 */
	public function close() {
		foreach ( $this->linked as $key => $value ) {
			mssql_close ( $value );
		}
	}
	/* (non-PHPdoc)
	 * @see wind/base/WDbAdapter#dispose()
	 */
	public function dispose() {
		foreach ( $this->linked as $key => $value ) {
			mssql_close ( $value );
			unset ( $this->linked [$key] );
		}
		$this->linking = null;
	}
	/**
	 * 取得mysql版本号
	 * @param string|int|resource $key 数据库连接标识
	 * @return string
	 */
	public function getVersion($key = '') {
	
	}

	/**
	 * 切换数据库
	 * @see wind/base/WDbAdapter#changeDB()
	 * @param string $databse 要切换的数据库
	 * @param string|int|resource $key 数据库连接标识
	 * @return boolean
	 */
	public function changeDB($database, $key = '') {
		return $this->read ( "USE $database", $key );
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WDbAdapter#error()
	 */
	protected function error() {
	
	}
}