<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-11
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
class WindMySql extends WindDbAdapter {
	/* (non-PHPdoc)
	 * @see wind/base/WDbAdapter#connect()
	 */
	public function connect($config, $key) {
		$this->key = $key;
		if (! ($this->linked[$key] = $this->getLinked ( $key ))) {
			$this->linked[$key] =  $config['pconnect'] ? mysql_pconnect ( $config['host'], $config ['dbuser'], $config ['dbpass'] ) : mysql_connect ( $config['host'], $config ['dbuser'], $config ['dbpass'], $config['force'] );
			if ($config ['dbname'] && is_resource ($this->linked[$key])) {
				$this->changeDB ( $config ['dbname'], $key);
			}
			$this->setCharSet ( $config['charset'], $key );
			if (!isset ( $this->config [$key] )) {
				$this->config [$key] = $config;
			}
		}
		return $this->linked[$key];
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WDbAdapter#query()
	 */
	public function query($sql, $key = '',$optype = '') {
		$this->getExecDbLink ($optype,$key);
		$this->query = mysql_query ( $sql, $this->linked[$this->key] );
		$this->last_sql = $sql;
		$this->error();
		$this->logSql();
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
		while (($record = mysql_fetch_array ( $this->query, $fetch_type ))) {
			$result [] = $record;
		}
		return $result;
	}

	public function beginTrans($key = '') {
		if ($this->transCounter == 0) {
			$this->write ( 'START TRANSACTION', $key );
		} elseif ($this->transCounter && $this->enableSavePoint) {
			$savepoint = 'savepoint_' . $this->transCounter;
			$this->write ( "SAVEPOINT `{$savepoint}`", $key );
			array_push ( $this->savepoint, $savepoint );
		}
		++ $this->transCounter;
		return true;
	}
	
	public function commitTrans($key = '') {
		if ($this->transCounter <= 0) {
			throw new WindSqlException(WindSqlException::DB_QUERY_TRAN_BEGIN);
		}
		--$this->transCounter;
		if ($this->transCounter == 0) {
			if ($this->last_errstr) {
				$this->write ( 'ROLLBACK',$key );
			} else {
				$this->write ( 'COMMIT',$key );
			}
		} elseif ($this->enableSavePoint) {
			$savepoint = array_pop ( $this->savepoint );
			if ($this->last_errstr) {
				$this->write ( "ROLLBACK TO SAVEPOINT `{$savepoint}`" );
			}
		}
	}
	/* (non-PHPdoc)
	 * @see wind/base/WDbAdapter#close()
	 */
	public function close() {
		foreach ( $this->linked as $key => $value ) {
			mysql_close ( $value );
		}
	}
	/* (non-PHPdoc)
	 * @see wind/base/WDbAdapter#dispose()
	 */
	public function dispose() {
		foreach ( $this->linked as $key => $value ) {
			mysql_close ( $value );
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
		$link = is_resource ( $key ) ? $key : $this->getLinked ( $key );
		return mysql_get_server_info ( $link );
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
		$this->last_errstr = mysql_error ();
		$this->last_errcode = mysql_errno ();
		if ($this->last_errstr || $this->last_errcode) {
			throw new WindSqlException ( $this->last_errstr, $this->last_errcode );
		}
	}
}