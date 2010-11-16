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
abstract class WSqlBuilder {
	
	protected $compare = array ('gt' => '>', 'egt' => '>=', 'lt' => '<', 'elt' => '<=', 'eq' => '=', 'neq' => '!=', 'in' => 'IN', 'notin' => 'NOT IN', 'notlike' => 'NOT LIKE', 'like' => 'LIKE' );
	protected $logic = array ('and' => 'AND', 'or' => 'OR', 'xor' => 'XOR' );
	protected $group = array ('left' => '(', 'right' => ')' );
	
	/**
	 * @param unknown_type $table
	 */
	public abstract function buildTable($table = array());
	/**
	 * @param unknown_type $distinct
	 */
	public abstract function buildDistinct($distinct = false);
	/**
	 * @param unknown_type $field
	 */
	public abstract function buildField($field = array());
	/**
	 * @param unknown_type $join
	 */
	public abstract function buildJoin($join = array());
	/**
	 * @param unknown_type $where
	 */
	public abstract function buildWhere($where = array());
	/**
	 * @param unknown_type $group
	 */
	public abstract function buildGroup($group = array());
	/**
	 * @param unknown_type $order
	 */
	public abstract function buildOrder($order = array());
	/**
	 * @param unknown_type $having
	 */
	public abstract function buildHaving($having = '');
	/**
	 * @param unknown_type $limit
	 * @param unknown_type $offset
	 */
	public abstract function buildLimit($limit = 0, $offset = 0);
	/**
	 * @param unknown_type $data
	 */
	public abstract function buildSet($data);
	/**
	 * @param unknown_type $setData
	 */
	public abstract function buildData($setData);
	/**
	 * @param unknown_type $value
	 */
	public abstract function escapeString($value);
	
	/**
	 * @param unknown_type $option
	 * @return string
	 */
	public function getInsertSql($option) {
		return sprintf ( "INSERT%s%sVALUES%s", $this->buildTable ( $option ['table'] ), $this->buildField ( $option ['field'] ), $this->buildData ( $option ['data'] ) );
	}
	/**
	 * @param unknown_type $option
	 * @return string
	 */
	public function getUpdateSql($option) {
		return sprintf ( "UPDATE%sSET%s%s%s%s", $this->buildTable ( $option ['table'] ), $this->buildSet ( $option ['set'] ), $this->buildWhere ( $option ['where'] ), $this->buildOrder ( $option ['order'] ), $this->buildLimit ( $option ['limit'] ) );
	}
	/**
	 * @param unknown_type $option
	 * @return string
	 */
	public function getDeleteSql($option) {
		return sprintf ( "DELETE FROM%s%s%s%s", $this->buildTable ( $option ['table'] ), $this->buildWhere ( $option ['where'] ), $this->buildOrder ( $option ['order'] ), $this->buildLimit ( $option ['limit'] ) );
	}
	/**
	 * @param unknown_type $option
	 * @return string
	 */
	public function getSelectSql($option) {
		return sprintf ( "SELECT%s%sFROM%s%s%s%s%s%s%s", $this->buildDistinct ( $option ['distinct'] ), $this->buildField ( $option ['field'] ), $this->buildTable ( $option ['table'] ), $this->buildJoin (), $this->buildWhere ( $option ['where'] ), $this->buildGroup ( $option ['group'] ), $this->buildHaving ( $option ['having'] ), $this->buildOrder ( $option ['order'] ), $this->buildLimit ( $option ['limit'], $option ['offset'] ) );
	}
	
	/**
	 * @param unknown_type $option
	 * @return string
	 */
	public function getReplaceSql($option){
		return sprintf ( "REPLACE%s%sSET%s", $this->buildTable ( $option ['table'] ), $this->buildField ( $option ['field'] ), $this->buildData ( $option ['data'] ) );
	}
	
	/**
	 * @param unknown_type $array
	 * @return number|number
	 */
	public function getDimension($array = array()) {
		$dim = 0;
		foreach ($array as $value ) {
			return  is_array($value) ? $dim+=2 : ++$dim;
		}
		return $dim;
	}
	
	/**
	 * @param unknown_type $data
	 * @return string
	 */
	public function buildSingleData($data) {
		foreach ( $data as $key => $value ) {
			$data [$key] = $this->escapeString ( $value );
		}
		return $this->sqlFillSpace('(' . implode ( ',', $data ) . ')');
	}
	
	/**
	 * @param unknown_type $multiData
	 * @return string
	 */
	public function buildMultiData($multiData) {
		$iValue = '';
		foreach ( $multiData as $data ) {
			$iValue .= $this->buildSingleData ( $data );
		}
		return $iValue;
	}
	
	/**
	 * @param unknown_type $value
	 * @return string
	 */
	public function sqlFillSpace($value) {
		return str_pad ( $value, strlen ( $value ) + 2, " ", STR_PAD_BOTH );
	}
}