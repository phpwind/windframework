<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-14
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
class WindTable {
	
	protected $distributed = null;
	protected $conn = null;
	protected $sqlBuilder = null;
	protected $table = '';
	protected $pk = '';
	
	/**
	 * @param array $table_config
	 * @param array $config
	 */
	public function __construct($table_config = array(), $db_config = array()) {
		$className = L::import ( 'WIND:component.db.WindConnectionManager' );
		$this->distributed = L::getInstance ( $className, array ($db_config ) );
		$this->conn = $this->distributed->getConnection ( $this->table_config [IWindDbConfig::CONN_IDENTITY], $this->table_config [IWindDbConfig::CONN_TYPE] );
		$this->sqlBuilder = $this->conn->getSqlBuilder ();
		$this->table = $table_config[IWindDbConfig::TABLE_NAME];
	}
	
	public function update($field,$fValue,$where = '',$wValue=array(),$order = array(),$limit = 0) {
		  $this->sqlBuilder
			 ->from($this->table)
			 ->set($field,$fValue)
			 ->where($where,$wValue)
			 ->order($order)
			 ->limit($limit)
			 ->update();
		  return true;
	}
	
	public function insert($data, $field = array()) {
		empty ( $field ) && list ( $field, $data ) = $this->parseData ( $data );
		$this->sqlBuilder
			 ->from ( $this->table )
			 ->field ( $field )
			 ->data ( $data )
			 ->insert ();
		return true;
	}
	
	public function replace($data, $field = array()) {
		empty ( $field ) && list ( $field, $data ) = $this->parseData ( $data );
		$this->sqlBuilder
					->from ( $this->table )
					->field ( $field )
					->data ( $data )
					->replace ();
		return true;
	}
	
	public function delete($where,$value=array(), $order = array(), $limit = 0) {
	
		 $this->sqlBuilder->from ( $this->table )
						 ->where($where,$value)
						 ->order($order)
						 ->limit($limit)
						 ->delete();
		 return true;
	}
	
	
	public function find($field,$where = '',$wValue = array(),$order = array(),$group = array(),$having = '',$hValue = array()) {
		 return  $this->sqlBuilder->from($this->table)
		 				 ->field($field)
						 ->where($where,$wValue)
						 ->group($group)
						 ->having($having,$hValue)
						 ->order($order)
						 ->limit(1)
						 ->select()
						 ->getRow();
	}
	
	public function findAll($field,$where = '',$wValue = array(),$order = array(),$page = array(),$group = array(),$having = '',$hValue = array()) {

		return $this->sqlBuilder->from($this->table)
						 ->field($field)
						 ->where($where,$wValue)
						 ->group($group)
						 ->having($having,$hValue)
						 ->order($order)
						 ->limit($page[0],$page[1])
						 ->select()
						 ->getAllRow();
	}

	public function count($where = '',$wValue = array(),$group = array(),$having = '',$hValue = array()) {
		$result = $this->sqlBuilder->field(' COUNT(*) as total')
							 	->from($this->table)
						 		->where($where,$wValue)
						 		->group($group)
						 		->having($having,$hValue)
							 	->select()
							 	->getRow();
		return (int)$result['total'];
	}
	
	private function parseData($data) {
		$key = array_keys ( $data );
		if (is_string ( $key [0] )) {
			$rows = count ( $data [$key [0]] );
			$tmp_data = $field = array ();
			for($i = 0; $i < $rows; $i ++) {
				foreach ( $data as $key => $value ) {
					$fvalues = array_values($field);
					if(!in_array($key,$fvalues)){
						$field [] = $key;
					}
					if (is_array ( $value )) {
						$tmp_data [$i] [] = $value [$i];
						unset ( $data [$key] [$i] );
					} else {
						$tmp_data [] = $value;
					}
				}
			}
		}
		$data = $tmp_data ? $tmp_data : $data;
		return array ($field, $data );
	}
	
	public function getLastInsertId() {
		return $this->conn->getLastInsertId();
	}
	
	public function getAffectedRows(){
		return $this->conn->getAffectedRows();
	}
}