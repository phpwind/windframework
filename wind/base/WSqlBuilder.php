<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-11
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
abstract class WSqlBuilder{
	
	public $sql = array(
		'SELECT','DISTINCT','FIELD','FROM','TABLE','UNION'
	);
	public abstract function getBuildedSql();
	public abstract function buildTable();
	public abstract function buildDistinct();
	public abstract function buildField();
	public abstract function buildUnion();
	public abstract function buildJoin();
	public abstract function buildWhere();
	public abstract function buildGroup();
	public abstract function buildOrder();
	public abstract function buildHaving();
	public abstract function buildLimit();
	public abstract function buildSet();
	public abstract function buildValue();
	public abstract function pauseParse();
	public abstract function getInsertSql();
	public abstract function getUpdateSql();
	public abstract function getDeleteSql();
	public abstract function getSelectSql();
	
	public function escapeString(){
		
	}
}