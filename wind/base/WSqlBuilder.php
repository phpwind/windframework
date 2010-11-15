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
	
	public abstract function buildTable($table);
	public abstract function buildDistinct($distinct);
	public abstract function buildField($field);
	public abstract function buildJoin($join);
	public abstract function buildWhere($order);
	public abstract function buildGroup($group);
	public abstract function buildOrder($order);
	public abstract function buildHaving($having);
	public abstract function buildLimit($limit,$offset);
	public abstract function buildSet($data);
	public abstract function buildData($setData);
	public abstract function escapeString($value);
	public  function getInsertSql($option){
		return sprintf("INSERT  %s %s VALUES %s",$this->buildTable($option['table']),$this->buildField($option['field']),$this->buildData($option['data']));
	}
	public  function getUpdateSql($option){
		return sprintf("UPDATE  %s  SET %s %s %s %s",$this->buildTable($option['table']),$this->buildSet($option['set']),$this->buildWhere($optiion['where']),$this->buildOrder($optiion['order']),$this->buildLimit($option['limit']));
	}
	public  function getDeleteSql($option){
		return sprintf("DELETE FROM %s %s %s %s",$this->buildTable($optiion['table']),$this->buildWhere($optiion['where']),$this->buildOrder($optiion['order']),$this->buildLimit($option['limit']));
	}
	public  function getSelectSql($option){
		return sprintf("SELECT %s  %s  FROM %s %s %s %s %s %s %s",$this->buildDistinct($option['distinct']),$this->buildField($option['field']),
			   $this->buildTable($option['table']),$this->buildJoin(),$this->buildWhere($option['where']),$this->buildGroup($option['group']),
			   $this->buildHaving($option['having']),$this->buildOrder($option['order']),$this->buildLimit($option['limit']));
	}


	public function getDimension($array = array()){
		if(!is_array($array)) return 0;			
		static $dim = 0;
		foreach($array as $value){
			$dim++;
			$this->getDimension($value);
		}
		return $dim + 1;
    }

	public function buildSingleData($data){
		foreach($data as $key=>$value){
			$data[$key] = $this->escapeString($value);
		}
		return '('.implode(',',$data).')';
	}

	public function buildMultiData($multiData){
		$iValue = '';
		foreach($multiData as $data){
			$iValue .= $this->buildSingleData($data);
		}
		return $iValue;
	}
}