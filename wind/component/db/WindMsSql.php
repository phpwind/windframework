<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-18
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class WindMsSql extends WindDbAdapter {
	public function connect($config, $key) {
		if (! is_array ( $config ) || empty ( $config )) {
			throw new WindSqlException ( "database config is not correct", 1 );
		}
		if (! isset ( $key )) {
			throw new WindSqlException ( "you must define master and slave database", 1 );
		}
		$host = $config ['dbport'] ? $config ['dbhost'] . ':' . $config ['dbport'] : $config ['dbhost'];
		$pconnect = $config ['pconnect'] ? $config ['pconnect'] : $this->pconnect;
		$force = $config ['force'] ? $config ['force'] : $this->force;
		$charset = $config ['charset'] ? $config ['charset'] : $this->charset;
		$this->key = $key;
		if (! ($this->linked [$key] = $this->getLinked ( $key ))) {
			$this->linked [$key] = $pconnect ? mssql_pconnect ( $host, $config ['dbuser'], $config ['dbpass'] ) : mssql_connect ( $host, $config ['dbuser'], $config ['dbpass'], $force );
			if ($config ['dbname'] && is_resource ( $this->linked [$key] )) {
				$this->changeDB ( $config ['dbname'], $key );
			}
			$this->setCharSet ( $charset, $key );
			if (! isset ( $this->config [$key] )) {
				$this->config [$key] = $config;
			}
		}
		return $this->linked [$key];
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
			throw new WindSqlException ( 'The Query is not validate handle or resource', 1 );
		}
		if (! in_array ( $fetch_type, array (1, 2, 3 ) )) {
			throw new WindSqlException ( 'The fetch_type is not validate handle or resource', 1 );
		}
		$result = array ();
		while ( ($record = mssql_fetch_array ( $this->query, $fetch_type )) ) {
			$result [] = $record;
		}
		return $result;
	}
	public function getMetaTables() {
	
	}
	public function getMetaColumns() {
	
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
	 * @param string $charset 字符集
	 * @param string | int $key 数据库连接标识
	 * @return boolean
	 */
	public function setCharSet($charset, $key = '') {
		$version = ( int ) substr ( $this->getVersion ( $key ), 0, 1 );
		if ($version > 4) {
			$this->read ( "SET NAMES '" . $charset . "'", $key );
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
	public function changeDB($database, $key = '') {
		return $this->read ( "USE $database", $key );
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WDbAdapter#error()
	 */
	protected function error() {
	
	}
}