<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-11
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:component.db.base.WindSqlBuilder');
/**
 * mysql常用sql语句组装器
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
final class WindMySqlBuilder extends WindSqlBuilder { 
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#getInsertSql()
	 */
	public function getInsertSql() {
		$sql = sprintf ( self::SQL_INSERT.'%s(%s)'.self::SQL_VALUES.'%s', 
			$this->buildFrom (), 
			$this->buildField (), 
			$this->buildData () 
		);
		$this->reset();
		return $sql;
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#getUpdateSql()
	 */
	public function getUpdateSql() {
		$sql = sprintf ( self::SQL_UPDATE.'%s'.self::SQL_SET.'%s%s%s%s', 
			$this->buildFrom (), 
			$this->buildSet (), 
			$this->buildWhere (), 
			$this->buildOrder (), 
			$this->buildLimit () 
		);
		$this->reset();
		return $sql;
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#getDeleteSql()
	 */
	public function getDeleteSql() {
		$sql = sprintf ( self::SQL_DELETE.' '.self::SQL_FROM.'%s%s%s%s', 
			$this->buildFrom (), 
			$this->buildWhere (), 
			$this->buildOrder (), 
			$this->buildLimit () 
		);
		$this->reset();
		return $sql;
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#getSelectSql()
	 */
	public function getSelectSql() {
		$sql = sprintf ( self::SQL_SELECT.'%s%s'.self::SQL_FROM.'%s%s%s%s%s%s%s', 
			$this->buildDistinct (), 
			$this->buildField (), 
			$this->buildFROM (), 
			$this->buildJoin (), 
			$this->buildWhere (), 
			$this->buildGroup (), 
			$this->buildHaving (), 
			$this->buildOrder (), 
			$this->buildLimit () 
			);
		$this->reset();
		return $sql;
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#getReplaceSql()
	 */
	public function getReplaceSql(){
		$sql = sprintf ( self::SQL_REPLACE.'%s(%s)'.self::SQL_SET.'%s', 
			$this->buildFROM (), 
			$this->buildField (), 
			$this->buildData () 
		);
		$this->reset();
		return $sql;
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#getReplaceSql()
	 */
	public function getLastInsertIdSql(){
		return sprintf (self::SQL_SELECT.'%s','LAST_INSERT_ID() AS insertId');
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#getReplaceSql()
	 */
	public function getAffectedSql($ifquery){
		$rows = $ifquery ? 'FOUND_ROWS()' : 'ROW_COUNT()';
		return sprintf (self::SQL_SELECT.'%s',"$rows AS afftectedRows");
	}

	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#getMetaTableSql()
	 */
	public function getMetaTableSql($schema){
		if(empty($schema)){
			throw new WindSqlException (WindSqlException::DB_EMPTY);
		}
		return 'SHOW TABLES FROM '.$schema;
	}
	
	/* (non-PHPdoc)
	 * @see wind/component/db/base/WindSqlBuilder#getMetaColumnSql()
	 */
	public function getMetaColumnSql($table){
		if(empty($table)){
			throw new WindSqlException (WindSqlException::DB_TABLE_EMPTY);
		}
		return 'SHOW COLUMNS FROM '.$table;
	}
	
	
	
	
}

