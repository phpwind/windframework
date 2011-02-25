<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-11
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:component.db.drivers.AbstractWindSqlBuilder');
/**
 * mysql常用sql语句组装器
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
final class WindMySqlBuilder extends AbstractWindSqlBuilder {
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#getInsertSql()
	 */
	public function getInsertSql() {
		$sql = sprintf('INSERT %s(%s) VALUES %s', $this->buildFrom(), $this->buildField(), $this->buildData());
		$this->reset();
		return $sql;
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#getUpdateSql()
	 */
	public function getUpdateSql() {
		$sql = sprintf('UPDATE %s SET %s%s%s%s', $this->buildFrom(), $this->buildSet(), $this->buildWhere(), $this->buildOrder(), $this->buildLimit());
		$this->reset();
		return $sql;
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#getDeleteSql()
	 */
	public function getDeleteSql() {
		$sql = sprintf('DELETE FROM %s%s%s%s', $this->buildFrom(), $this->buildWhere(), $this->buildOrder(), $this->buildLimit());
		$this->reset();
		return $sql;
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#getSelectSql()
	 */
	public function getSelectSql() {
		$sql = sprintf('SELECT %s%s FROM %s%s%s%s%s%s%s', $this->buildDistinct(), $this->buildField(), $this->buildFROM(), $this->buildJoin(), $this->buildWhere(), $this->buildGroup(), $this->buildHaving(), $this->buildOrder(), $this->buildLimit());
		$this->reset();
		return $sql;
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#getReplaceSql()
	 */
	public function getReplaceSql() {
		$sql = sprintf('REPLACE %s(%s) VALUES %s', $this->buildFROM(), $this->buildField(), $this->buildData());
		$this->reset();
		return $sql;
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#getReplaceSql()
	 */
	public function getLastInsertIdSql() {
		return sprintf('SELECT ' . '%s', 'LAST_INSERT_ID() AS insertId');
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#getReplaceSql()
	 */
	public function getAffectedSql($ifquery = true) {
		$rows = $ifquery ? 'FOUND_ROWS()' : 'ROW_COUNT()';
		return sprintf('SELECT ' . '%s', "$rows AS afftectedRows");
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#getMetaTableSql()
	 */
	public function getMetaTableSql($schema) {
		if (empty($schema)) {
			throw new WindSqlException('', WindSqlException::DB_EMPTY);
		}
		return 'SHOW TABLES FROM ' . $schema;
	}
	
	/* (non-PHPdoc)
	 * @see wind/component/db/base/WindSqlBuilder#getMetaColumnSql()
	 */
	public function getMetaColumnSql($table) {
		if (empty($table)) {
			throw new WindSqlException('', WindSqlException::DB_TABLE_EMPTY);
		}
		return 'SHOW COLUMNS FROM ' . $table;
	}

}

