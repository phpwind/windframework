<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-11
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class WMySqlBuilder extends WSqlBuilder{

	public  function buildTable(){
	}
	public  function buildDistinct($distinct = false){
		return $distinct ' DISTINCT ' : '';
	}
	public  function buildField(){
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
	public  function buildLimit($limit,$offset = 0){
		return ($sql =  $limit > 0 ? ' LIMIT '.$limit : '') ?  $offset > 0 ? $sql .' OFFSET '.$offset : $sql : '';
	}
	
	public  function buildData($data){
		if(empty($data) || !is_array($data)) {
			throw new WSqlExceptiion($data);
		}
		return $this->getDimension($data) == 1 ? $this->buildSingleData($data) : $this->buildMultiData($data);
	}




	public  function escapeString($value){
		return $value;
	}
}