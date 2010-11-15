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

class WMySqlBuilder extends WSqlBuilder{

	public  function buildTable($table){
		if(empty($table) || !is_string($table) || !is_array($table)){
			throw new WSqlException('table is not mepty');
		}
		$table = is_string($table) ? explode(',',$table) : $table;
		$tableList = '';
		foreach($table as $key=>$value){
			if(is_int($key)){
				$tableList .=  $tableList ? ','.$value : $value;
			}
			if(is_string($key)){
				$tableList .=  $tableList ? ','.$this->getAlias($key,$as,$value) : $this->getAlias($key,$as,$value);
			}
		}
		return $tableList;
		
	}
	
	public  function buildDistinct($distinct){
		return $distinct ? ' DISTINCT ' : '';
	}
	public  function buildField($field){
		$fieldList = '';
		if(empty($field)) {
			$fieldList = '*';
		}
		if(!is_string($field) || !is_array($field)){
			throw new WSqlException('field is illegal');
		}
		$field = is_string($field) ? explode(',',$field) : $field;
		foreach($field as $key=>$value){
			if(is_int($key)){
				$fieldList .=  $fieldList ? ','.$value : $value;
			}
			if(is_string($key)){
				$fieldList .=  $fieldList ? ','.$this->getAlias($key,$as,$value) : $this->getAlias($key,$as,$value);
			}
		}
		return $fieldList;
	}

	public  function buildJoin($join){
		if(empty($join)) {
			return '';
		}
		if(is_string($join)){
			return $join;
		}
		foreach($join as $table=>$config){
			if(is_string($config)) {
				$joinContidion .= $joinContidion ? ','.$config : $config;
				continue;
			}
			if(is_array($config)){
				$table = $this->getAlias($table,$as,$config['alias']);
				$joinWhere = $config['where'] ? ' ON '.$config['where'] : '';
				$condition = $config['type'] .' JOIN '.$table.$joinWhere;
				$joinContidion .= $joinContidion ? ','.$condition : $condition;
			}
		}
		return $joinContidion;
	}
	public  function buildWhere(){

	}
	public  function buildGroup($group){
		return $group ? ' GROUP BY '.(is_array($group) ? implode(',',$group) : $group).' ' : '';
	}
	public  function buildOrder($order){
		$orderby = '';
		if(is_array($order)){
			foreach($order as $key=>$value){
				$orderby .= ($orderby ? ',' : '') . (!is_int($key) ? $key.' '.strtoupper($value) : $value);
			}
		}else{
			$orderby = $order;
		}
		return $orderby ? ' ORDER BY '.$orderby : '';
	}
	public  function buildHaving($having){
		return $having ? ' HAVING '.$having  : ' ';
	}
	public  function buildLimit($limit,$offset){
		return ($sql =  $limit > 0 ? ' LIMIT '.$limit : '') ?  $offset > 0 ? $sql .' OFFSET '.$offset : $sql : '';
	}
	
	public  function buildData($data){
		if(empty($data) || !is_array($data)) {
			throw new WSqlExceptiion($data);
		}
		return $this->getDimension($data) == 1 ? $this->buildSingleData($data) : $this->buildMultiData($data);
	}
	
	public function buildSet(){
		
	}




	public  function escapeString($value){
		return $value;
	}
	
	private function getAlias($name,$as =' ',$alias = ''){
		return ' '.($alias ?  $name.' '.strtoupper($as).' '.$alias :$name).' ';
	}
	
	
}