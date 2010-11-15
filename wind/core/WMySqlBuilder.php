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

	public  function buildTable($table,$as = ' ',$alias = ''){
		if(empty($table) || !is_string($table) || !is_array($table)){
			throw new WSqlException('table is not mepty');
		}
		$tableList = is_string($table) ? explode(',',$table) : $table;
		$standTable = '';
		foreach($tableList as $key=>$value){
			if(is_int($key)){
				$standTable .=  $standTable ? ','.$this->getStandardSqlInfo($value,$as,$alias) : $this->getStandardSqlInfo($value,$as,$alias);
			}
			if(is_string($key)){
				$standTable .=  $standTable ? ','.$this->getStandardSqlInfo($key,$as,$value) : $this->getStandardSqlInfo($key,$as,$value);
			}
		}
		return $standTable;
		
	}
	
	public  function buildDistinct($distinct){
		return $distinct ? ' DISTINCT ' : '';
	}
	public  function buildField($field){
		$rawField = '';
		if(empty($field)) $rawField = '*';
		if(!is_string($table) || !is_array($table)){
			throw new WSqlException('table is not mepty');
		}
		
	}
	public  function buildUnion(){

	}
	public  function buildJoin(){

	}
	public  function buildWhere(){

	}
	public  function buildGroup($group){
		return isset($group) ? ' GROUP BY '.$group : '';
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
		return isset($having) ? ' HAVING '.$having  : ' ';
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
	
	public function  getStandardSqlInfo($name,$as = ' ',$alias = ''){
		if( false !== strrpos($name,'`') ||  false !== strpos($name,'*')) return $name;
		$ifmatch = preg_match('/([\s| ])+/i',trim($name),$match);
		if($ifmatch && !strpos($name,')')){
			$name = explode($match[1],$name);
			$as = in_array(strtolower($as),array(' ','as')) ? $as : ' ';
			$name = in_array(strtolower($name[0]),array('distinct')) ? $name[0].'  `'.$name[count($name)-1].'`': '`'.$name[0].'`  '.strtoupper($as).' `'.$name[count($name)-1].'`';
		}else{
			$alias = $alias ? ' '.strtoupper($as).' '.$alias : '';
			$name = (strpos($name,')')   ? str_replace(array('(',')',' '),array('(`','`)',''),$name) : '`'.$name.'`').$alias;
		}
		return $name;
	}
}