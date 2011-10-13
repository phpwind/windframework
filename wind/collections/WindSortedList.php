<?php
/**
 * 表示键/值对的集合，这些键值对按键排序并可按照键和索引访问。
 * the last known user to change this file in the repository  <$LastChangedBy: yishuo $>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package wind.collections
 */
class WindSortedList implements IteratorAggregate, ArrayAccess, Countable {
	/**
	 * @var array 集合列表
	 */
	private $list = array();
	/**
	 * @var int 列表总数指示器
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
	public function __construct($data = array(), $readOnly = false) {
		$this->isReadOnly = $readOnly;
		if (false === empty($data)) {
			if (is_array($data)) {
				$this->list = $data;
			} elseif ($data instanceof WindList) {
				$this->list = $data->getList();
			} else {
				throw new WindException("Parameter type is incorrect");
			}
			$this->isFixedSize = true;
			$this->count = count($this->list);
		}
	
	}
	
	/**
	 * 将带有指定键和值的元素添加到 WindSortedList 对象。
	 * @param mixed $key
	 * @param mixed $value
	 */
	public function add($key, $value) {
		$this->checkPermissions();
		if (isset($this->list[$key])) {
			throw new WindException($key . ' key already exists in the collection');
		}
		$this->list[$key] = $value;
		++$this->count;
		return true;
	}
	/**
	 * 确定 WindSortedList对象是否包含特定的键。
	 * @param mixed $key
	 * @return boolean
	 */
	public function contains($key) {
		return $this->containsKey($key);
	}
	
	/**
	 * 根据指定的索引确定 WindSortedList对象是否包含特定的键。
	 * @param mixed $key
	 * @return boolean
	 */
	public function containsIndex($index) {
		return $this->containsKey($this->getKey($index));
	}
	
	/**
	 * 确定 WindSortedList对象是否包含特定的键。
	 * @param mixed $key
	 * @return boolean
	 */
	public function containsKey($key) {
		return 0 <= $this->indexOfKey($key);
	}
	
	/**
	 * 确定 WindSortedList对象是否包含特定值。
	 * @param mixed $value
	 * @return boolean
	 */
	public function containsValue($value) {
		return 0 <= $this->indexOfValue($value);
	}
	
	/**
	 * 获取 WindSortedList 对象的指定索引处的键。
	 * @param int $index 指定的索引
	 * @return mixed
	 */
	public function getKey($index) {
		$keys = $this->getKeyList();
		if (false === isset($keys[$index])) {
			throw new WindException('Index out of range, is indeed the range should be between 0 to ' . $this->count);
		}
		return $keys[$index];
	}
	
	/**
	 * 获取WindSortedList 对象中的键。
	 * @return mixed
	 */
	public function getKeyList() {
		return array_keys($this->list);
	}
	
	/**
	 * 获取WindSortedList 对象中的值。
	 * @return mixed
	 */
	public function getValueList() {
		return array_values($this->list);
	}
	
	/**
	 * 返回WindSortedList 对象中指定键的从零开始的索引。 
	 * @param mixed $key
	 * @return mixed
	 */
	public function indexOfKey($key) {
		return false !== ($index = array_search($key, $this->getKeyList(), true)) ? $index : -1;
	}
	
	/**
	 * 返回指定的值在 SortedList 对象中第一个匹配项的从零开始的索引。
	 * @param mixed $value
	 * @return mixed
	 */
	public function indexOfValue($value) {
		return false !== ($index = array_search($value, $this->getValueList(), true)) ? $index : -1;
	}
	
	/**
	 * 从WindSortedList中返回指定键的的值
	 * @param int $key
	 * @return mixed
	 */
	public function item($key) {
		if (false === isset($this->list[$key])) {
			throw new WindException($key . ' is not exists in the collection');
		}
		return $this->list[$key];
	}
	/**
	 * 从WindSortedList中返回指定索引的的值
	 * @param int $index
	 * @return mixed
	 */
	public function itemAt($index) {
		$key = $this->getKey($index);
		return $this->list[$key];
	}
	/**
	 * 从 SortedList 对象中移除带有指定键的元素。
	 * @param mixed $key
	 * @return mixed
	 */
	public function remove($key) {
		$this->checkPermissions();
		if (false === isset($this->list[$key])) {
			throw new WindException($key . ' is not exists in the collection');
		}
		$item = $this->list[$key];
		unset($this->list[$key]);
		--$this->count;
		return $item;
	}
	/**
	 * 移除 WindSortedList对象的指定索引处的元素。
	 * @param int $index
	 * @return mixed
	 */
	public function removeAt($index) {
		$this->checkPermissions();
		$key = $this->getKey($index);
		$item = $this->list[$key];
		unset($this->list[$key]);
		--$this->count;
		return $item;
	}
	/**
	 * 替换WindSortedList对象中指定索引处的值。
	 * @param int $index
	 * @param mixed $value
	 * @return boolean
	 */
	public function setByIndex($index, $value) {
		$this->checkPermissions(false);
		$key = $this->getKey($index);
		$this->list[$key] = $value;
		return true;
	}
	/**
	 * 替换WindSortedList对象中指定键处的值。
	 * 如果指定的键存在，那么替换，否则新增
	 * @param mixed $key
	 * @param mixed $value
	 * @return mixed
	 */
	public function setByKey($key, $value) {
		$this->checkPermissions(false);
		if (isset($this->list[$key])) {
			$item = $this->list[$key];
			$this->list[$key] = $value;
			return $item;
		}
		$this->list[$key] = $value;
		++$this->count;
		return $value;
	}
	
	/**
	 * 从 SortedList 对象中移除所有元素。
	 * @return boolean
	 */
	public function clear() {
		$this->checkPermissions();
		$this->count = 0;
		$this->list = array();
		return true;
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
	/**
	 * 返回集合的总数
	 * @return number
	 */
	public function getCount() {
		return $this->count;
	}
	/* 计算集合总数
	 * @see Countable#count()
	 */
	public function count() {
		return $this->getCount();
	}
	/**
	 * 取得WindSortedList集合迭代器
	 * @return ArrayIterator
	 */
	public function getIterator() {
		return new ArrayIterator($this->list);
	}
	/* 
	 * @see ArrayAccess#offsetExists()
	 */
	public function offsetExists($offset) {
		if (is_int($offset)) {
			return $this->containsIndex($offset);
		}
		return $this->containsKey($offset);
	}
	/* 
	 * @see ArrayAccess#offsetGet()
	 */
	public function offsetGet($offset) {
		if (is_int($offset)) {
			return $this->itemAt($offset);
		}
		return $this->item($offset);
	}
	/* 
	 * @see ArrayAccess#offsetSet()
	 */
	public function offsetSet($offset, $value) {
		if (is_int($offset)) {
			return $this->setByIndex($offset, $value);
		}
		return $this->setByKey($offset, $value);
	}
	/* 
	 * @see ArrayAccess#offsetUnset()
	 */
	public function offsetUnset($offset) {
		if (is_int($offset)) {
			return $this->removeAt($offset);
		}
		return $this->remove($offset);
	}
	/**
	 * 创建 WindSortedList对象的浅表副本。
	 * @return WindSortedList
	 */
	public function __clone() {
		return new self($this->list, $this->isReadOnly);
	}
	
	private function checkPermissions($ifcheckFixedSize = true) {
		if ($this->isReadOnly) {
			throw new WindException('The list of read-only');
		}
		if ($this->isFixedSize && $ifcheckFixedSize) {
			throw new WindException("The list size is fixed");
		}
	}

}