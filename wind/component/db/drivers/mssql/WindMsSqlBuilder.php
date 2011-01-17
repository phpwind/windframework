<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-18
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:component.db.base.WindSqlBuilder');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindMsSqlBuilder extends WindSqlBuilder {
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#getInsertSql()
	 */
	public function getInsertSql() {
		$sql = sprintf(self::SQL_INSERT . '%s(%s)' . self::SQL_VALUES . '%s', $this->buildFrom(), $this->buildField(), $this->buildData());
		$this->reset();
		return $sql;
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#getUpdateSql()
	 */
	public function getUpdateSql() {
		$sql = sprintf(self::SQL_UPDATE . '%s' . self::SQL_SET . '%s%s%s', $this->buildFrom(), $this->buildSet(), $this->buildWhere(), $this->buildOrder());
		$this->reset();
		return $sql;
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#getDeleteSql()
	 */
	public function getDeleteSql() {
		$sql = sprintf(self::SQL_DELETE . ' ' . self::SQL_FROM . '%s%s%s', $this->buildFrom(), $this->buildWhere(), $this->buildOrder());
		$this->reset();
		return $sql;
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#getSelectSql()
	 */
	public function getSelectSql() {
		$sql = sprintf(self::SQL_SELECT . '%s%s' . self::SQL_FROM . '%s%s%s%s%s%s', $this->buildDistinct(), $this->buildField(), $this->buildFROM(), $this->buildJoin(), $this->buildWhere(), $this->buildGroup(), $this->buildHaving(), $this->buildOrder());
		$this->reset();
		return $sql;
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#getReplaceSql()
	 */
	public function getReplaceSql() {
		$sql = sprintf(self::SQL_REPLACE . '%s(%s)' . self::SQL_SET . '%s', $this->buildFROM(), $this->buildField(), $this->buildData());
		$this->reset();
		return $sql;
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#getReplaceSql()
	 */
	public function getLastInsertIdSql() {
		return sprintf(self::SQL_SELECT . '%s', '@@IDENTITY ' . self::SQL_AS . ' insertId');
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#getReplaceSql()
	 */
	public function getAffectedSql($ifquery = true) {
		return sprintf(self::SQL_SELECT . '%s', '@@ROWCOUNT ' . self::SQL_AS . ' affectedRows');
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#getMetaTableSql()
	 */
	public function getMetaTableSql($schema) {
		$schema = $schema ? $schema . '.' : '';
		return "SELECT name,object_id FROM {$schema}sys.all_objects WHERE type = 'U'";
	}
	
	/* (non-PHPdoc)
	 * @see wind/component/db/base/WindSqlBuilder#getMetaColumnSql()
	 */
	public function getMetaColumnSql($table) {
		if (empty($table)) {
			throw new WindSqlException('', WindSqlException::DB_TABLE_EMPTY);
		}
		$sql = $this->from('sys.objects', 'a')->field('b.name Field,b.max_length,b.precision,b.scale,b.is_nullable,b.is_identity')->innerJoin('sys.all_columns', 'a.object_id = b.object_id', 'b')->innerJoin('sys.types', 'b.system_type_id = c.system_type_id', 'c')->where('a.name = ? ', $table)->getSelectSql();
		return $sql;
	
	}

}