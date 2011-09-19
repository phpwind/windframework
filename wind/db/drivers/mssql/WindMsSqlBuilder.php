<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-18
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
Wind::import('WIND:db.drivers.AbstractWindSqlBuilder');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindMsSqlBuilder extends AbstractWindSqlBuilder {
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
		$sql = sprintf('UPDATE %s SET %s%s%s', $this->buildFrom(), $this->buildSet(), $this->buildWhere(), $this->buildOrder());
		$this->reset();
		return $sql;
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#getDeleteSql()
	 */
	public function getDeleteSql() {
		$sql = sprintf('DELETE FROM %s%s%s', $this->buildFrom(), $this->buildWhere(), $this->buildOrder());
		$this->reset();
		return $sql;
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#getSelectSql()
	 */
	public function getSelectSql() {
		$sql = sprintf('SELECT %s%s FROM %s%s%s%s%s%s', $this->buildDistinct(), $this->buildField(), $this->buildFROM(), $this->buildJoin(), $this->buildWhere(), $this->buildGroup(), $this->buildHaving(), $this->buildOrder());
		$this->reset();
		return $sql;
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#getReplaceSql()
	 */
	public function getReplaceSql() {
		return '';
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#getReplaceSql()
	 */
	public function getLastInsertIdSql() {
		return sprintf('SELECT ' . '%s', '@@IDENTITY AS insertId');
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#getReplaceSql()
	 */
	public function getAffectedSql($ifquery = true) {
		return sprintf('SELECT ' . '%s', '@@ROWCOUNT AS affectedRows');
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