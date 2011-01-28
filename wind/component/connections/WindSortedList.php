<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

/**
 * 表示键/值对的集合，这些键值对按键排序并可按照键和索引访问。
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindSortedList implements IteratorAggregate,ArrayAccess,Countable{
	
	/**
	 * @var array 集合列表
	 */
	private $list = array();
	/**
	 * @var string 列表总数指示器
	 */
	private $count = 0;
	/**
	 * @var boolean 列表是否只读
	 */
	private $isReadOnly = false;
	
	/**
	 * @var boolean 是否固定大小
	 */
	private $isFixedSize = false;

	/**
	 * WindList 实现有三种类别：只读、固定大小、可变大小。 
	 * 无法修改只读 WindList。
	 * 固定大小的 WindList 不允许添加或移除元素，但允许修改现有元素。
	 * 可变大小的 WindList 允许添加、移除和修改元素。
	 * @param boolean $readOnly 是否只读
	 * @param array|WindList $data 固定长度，如果指定了$data,那么这个WindList集合的长度是固定的，只许修改
	 */
	public function __construct($data = array(),$readOnly = false) {
		$this->isReadOnly = $readOnly;
		if(false === empty($data)){
			if(is_array($data)){
				$this->list = $data;
			}elseif($data instanceof WindList){
				$this->list = $data->getList();
			}else{
				throw new WindException("Parameter type is incorrect");
			}
			$this->isFixedSize = true;
			$this->count = count($this->list);
		}
		
	}
	
	public function add($key,$value){
		
	}
	
	public function contains($key){
		
	}
	
	public function containsKey($key){
		
	}
	
	public function containsValue($value){
		
	}
	
	public function getKey($index){
		
	}
	
	public function getKeyList(){
		
	}
	
	public function getValueList(){
		
	}
	
	public function indexOfKey(){
		
	}
	
	public function indexOfValue(){
		
	}
	
	public function remove(){
		
	}
	
	public function removeAt(){
		
	}
	
	public function setByIndex($index,$value){
		
	}
	
	public function setByKey($key,$key){
		
	}
	
	public function clear(){
		
	}
	
	public function getCount(){
		
	}
	
	/**
	 * 
	 */
	public function count() {
		
	}

	/**
	 * 
	 */
	public function getIterator() {
		
	}

	/**
	 * @param unknown_type $offset
	 */
	public function offsetExists($offset) {
		
	}

	/**
	 * @param unknown_type $offset
	 */
	public function offsetGet($offset) {
		
	}

	/**
	 * @param unknown_type $offset
	 * @param unknown_type $value
	 */
	public function offsetSet($offset, $value) {
		
	}

	/**
	 * @param unknown_type $offset
	 */
	public function offsetUnset($offset) {
		
	}
	
	public function __clone(){
		
	}

	
}