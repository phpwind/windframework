<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-18
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:component.drivers.AbstractWindDbAdapter');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindMsSql extends AbstractWindDbAdapter {
	/* (non-PHPdoc)
	 * @see wind/base/WDbAdapter#connect()
	 */
	protected function connect() {
		if (!is_resource($this->connection) || ($this->config[IWindDbConfig::FORCE])) {
			$host = isset($this->config[IWindDbConfig::PORT]) ? $this->config[IWindDbConfig::HOST] . ':' . $this->config[IWindDbConfig::PORT] : $this->config[IWindDbConfig::HOST];
			$user = $this->config[IWindDbConfig::USER];
			$pass = $this->config[IWindDbConfig::PASS];
			$pconnect = ('true' === $this->config[IWindDbConfig::PCONNECT] );
			$this->connection = $pconnect ? mssql_pconnect($host, $user, $pass) : mssql_connect($host, $user, $pass, $this->config[IWindDbConfig::FORCE]);
			$this->changeDB($this->config[IWindDbConfig::NAME]);
		}
		return $this->connection;
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WDbAdapter#query()
	 */
	public function query($sql) {
		$this->query = mssql_query($sql, $this->connection);
		$this->error($sql);
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see wind/component/db/base/WindDbAdapter#getAffectedRows()
	 */
	public function getAffectedRows() {
		$this->query('SELECT @@ROWCOUNT AS affectedRow');
		$row = $this->getRow();
		return (int) $row['affectedRow'];
	}
	
	/* (non-PHPdoc)
	 * @see wind/component/db/base/WindDbAdapter#getLastInsertId()
	 */
	public function getLastInsertId() {
		$this->query('SELECT @@IDENTITY as insertId');
		$row = $this->getRow();
		return (int) $row['insertId'];
	}
	
	/* (non-PHPdoc)
	 * @see wind/component/db/base/WindDbAdapter#getMetaTables()
	 */
	public function getMetaTables($schema = '') {
		$schema = $schema ? $schema : $this->getSchema();
		if (empty($schema)) {
			throw new WindSqlException('', WindSqlException::DB_EMPTY);
		}
		$this->query("SELECT name,object_id FROM {$schema}.sys.all_objects WHERE type = 'U'");
		return $this->getAllRow(IWindDbConfig::ASSOC);
	}
	
	/* (non-PHPdoc)
	 * @see wind/component/db/base/WindDbAdapter#getMetaColumns()
	 */
	public function getMetaColumns($table) {
		if (empty($table)) {
			throw new WindSqlException('', WindSqlException::DB_TABLE_EMPTY);
		}
		$this->query('SELECT b.name Field,b.max_length,b.precision,b.scale,b.is_nullable,b.is_identity FROM sys.objects a 
					  INNER JOIN sys.all_columns b ON a.object_id = b.object_id 
					  INNER JOIN sys.types c ON b.system_type_id = c.system_type_id where a.name =  ' . $this->escapeString($table));
		return $this->getAllRow(IWindDbConfig::ASSOC);
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WDbAdapter#getAllRow()
	 */
	public function getAllRow($fetch_type = IWindDbConfig::ASSOC) {
		if (!is_resource($this->query)) {
			throw new WindSqlException('', WindSqlException::DB_QUERY_LINK_EMPTY);
		}
		if (!in_array($fetch_type, array(1, 2, 3))) {
			throw new WindSqlException('', WindSqlException::DB_QUERY_FETCH_ERROR);
		}
		$result = array();
		while (($record = mssql_fetch_array($this->query, $fetch_type))) {
			$result[] = $record;
		}
		return $result;
	}
	
	/* (non-PHPdoc)
	 * @see wind/component/db/base/WindDbAdapter#getRow()
	 */
	public function getRow($fetch_type = IWindDbConfig::ASSOC) {
		return mssql_fetch_array($this->query, $fetch_type);
	}
	
	/**
	 *@see wind/component/db/base/WindDbAdapter#beginTrans()
	 */
	public function beginTrans() {

	}
	
	/**
	 * @see wind/component/db/base/WindDbAdapter#commitTrans()
	 */
	public function commitTrans() {

	}
	/* (non-PHPdoc)
	 * @see wind/base/WDbAdapter#close()
	 */
	public function close() {
		if (is_resource($this->connection)) {
			mssql_close($this->connection);
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
	 * 切换数据库
	 * @see wind/base/WDbAdapter#changeDB()
	 * @param string $databse 要切换的数据库
	 * @param string|int|resource $key 数据库连接标识
	 * @return boolean
	 */
	public function changeDB($database) {
		return mssql_select_db($database, $this->connection);
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WDbAdapter#error()
	 */
	protected function error($sql) {
		$this->last_sql = $sql;
		return true;
	}
}