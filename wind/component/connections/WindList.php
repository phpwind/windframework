<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

/**
 * WindList集合.
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindList implements IteratorAggregate, ArrayAccess, Countable {
	
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
	/**
	 * 向 WindList 中添加项。
	 * @param mixed $value
	 * @return boolean
	 */
	public function add($value) {
		return $this->insertAt($this->count, $value);
	}
	/**
	 * 在 WindList 中的指定索引处插入项。
	 * @param int $index
	 * @param mixed $value
	 * @return boolean
	 */
	public function insertAt($index, $value) {
		if($this->isFixedSize && $this->count === $index){
			throw new WindException("The list size is fixed");
		}
		if (false === $this->isReadOnly) {
			if ($this->count === $index) {
				$this->list[$this->count++] = $value;
			} elseif (0 <= $index && $this->count > $index) {
				array_splice($this->list, $index, 0, array($value));
				$this->count++;
			} elseif ($this->count < $index || 0 > $index) {
				throw new WindException('Index out of range, is indeed the range should be between 0 to '.$this->count);
			}
		} else {
			throw new WindException('The list of read-only');
		}
		return true;
	}
	/**
	 * 取得指定索引的项
	 * @param int $index 指定索引
	 * @return mixed
	 */
	public function itemAt($index) {
		if (false === isset($this->list[$index])) {
			throw new WindException('Index out of range, is indeed the range should be between 0 to '.$this->count);
		}
		return $this->list[$index];
	}
	/**
	 * 返回指定项的索引,返回-1表向没有指定项
	 * @param mixed $value 指定项
	 * @return int
	 */
	public function indexOf($value) {
		return false !== ($index = array_search($value, $this->list, true)) ? $index : -1;
	}
	/**
	 * 判断WindList中是否包含指定项
	 * @param mixed $value 指定项
	 * @return boolean
	 */
	public function contain($value) {
		return 0 <= $this->indexOf($value);
	}
	/**
	 * 判断WindList中是否包含指定的索引
	 * @param int $index 指定索引
	 * @return boolean
	 */
	public function containAt($index) {
		return 0 <= $index && $this->count < $index;
	}
	/**
	 * 修改WindList中指定索引的项
	 * @param int $index 指定索引
	 * @param mixed $value 要修改的项
	 * @return boolean
	 */
	public function modify($index,$value){
		$this->removeAt($index,true);
		$this->count++;
		$this->insertAt($index, $value);
		$this->count--;
		return true;
	}
	/**
	 * 从WindList中移除指定索引的项
	 * @param int $index 指定索引
	 * @return mixed
	 */
	public function removeAt($index,$force = false) {
		if ($this->isReadOnly) {
			throw new WindException('The list of read-only');
		}
		if($this->isFixedSize && false === $force){
			throw new WindException("The list size is fixed");
		}
		if ($index > $this->count || $index < 0) {
			throw new WindException('Index out of range, is indeed the range should be between 0 to '.$this->count);
		}
		$this->count--;
		if (0 === $index) {
			return array_shift($this->list);
		}
		if ($this->count - 1 === $index) {
			return array_pop($this->list);
		}
		$item = $this->list[$index];
		array_splice($this->list, $index, 1);
		return $item;
	}
	/**
	 * 从WindList中移除指定的项
	 * @param mixed $value 指定的项
	 * @return boolean
	 */
	public function remove($value) {
		return $this->removeAt($this->indexOf($value));
	}
	/**
	 * 清空WindList
	 */
	public function clear() {
		$this->count = 0;
		$this->list = array();
	}
	/**
	 * 将数组中的值合并到WindList
	 * @param array $array 要合并的数组
	 * @return boolean
	 */
	public function mergeFromArray(array $array) {
		foreach ($array as $value) {
			$this->add($value);
		}
		return true;
	}
	/**
	 * 将WindList集合合并到WindList
	 * @param WindList $list 要合并的WindList集合
	 * @return boolean
	 */
	public function mergeFromList(WindList $list) {
		foreach ($list as $value) {
			$this->add($value);
		}
		return true;
	}
	/**
	 * @return array
	 */
	public function getList() {
		return $this->list;
	}
	/**
	 * 取得集合的总数
	 * @return string
	 */
	public function getCount() {
		return $this->count;
	}
	/**
	 * 取得集合是否只读
	 * @return boolean
	 */
	public function getIsReadOnly() {
		return $this->isReadOnly;
	}
	/**
	 * 取得集合是否是固定大小
	 * @return boolean
	 */
	public function getIsFixedSize(){
		return $this->isFixedSize;
	}
	/* 
	 * 计算集合的总个数
	 * @see Countable#count()
	 */
	public function count() {
		return $this->getCount();
	}
	/**
	 * @param int $offset
	 */
	public function offsetExists($offset) {
		return $this->containAt($offset);
	}
	/**
	 * @param int $offset
	 */
	public function offsetGet($offset) {
		return $this->itemAt($offset);
	}
	/**
	 * @param int $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value) {
		if (null === $offset || $this->count === $offset) {
			return $this->add($value);
		}
		return $this->modify($offset, $value);
	}
	/**
	 * @param int $offset
	 */
	public function offsetUnset($offset) {
		return $this->removeAt($offset);
	}
	/**
	 * 取得集合的迭代器
	 */
	public function getIterator() {
		return new ArrayIterator($this->list);
	}

}