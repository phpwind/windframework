<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindList implements IteratorAggregate, ArrayAccess, Countable {
	
	/**
	 * @var array
	 */
	private $list = array();
	/**
	 * @var string
	 */
	private $count = 0;
	/**
	 * @var boolean
	 */
	private $isReadOnly = false;
	
	public function __construct($readOnly = false) {
		$this->isReadOnly = $readOnly;
	}
	
	/**
	 * @param mixed $value
	 * @return boolean
	 */
	public function add($value) {
		return $this->insertAt($this->count, $value);
	}
	
	/**
	 * @param int $index
	 * @param mixed $value
	 * @return boolean
	 */
	public function insertAt($index, $value) {
		
		if (false === $this->isReadOnly) {
			if ($this->count === $index) {
				$this->list[$this->count++] = $value;
			} elseif (0 <= $index && $this->count > $index) {
				array_splice($this->list, $index, 0, array($value));
				$this->count++;
			} elseif ($this->count < $index || 0 > $index) {
				throw new Exception('This list 1');
			}
		} else {
			throw new Exception('This list 2');
		}
		return true;
	}
	
	/**
	 * @param int $index
	 * @return mixed
	 */
	public function itemAt($index) {
		if (false === isset($this->list[$index])) {

		}
		return $this->list[$index];
	}
	
	/**
	 * @param mixed $value
	 * @return int
	 */
	public function indexOf($value) {
		return false !== ($index = array_search($value, $this->list, true)) ? $index : -1;
	}
	
	/**
	 * @param mixed $value
	 * @return boolean
	 */
	public function contain($value) {
		return 0 <= $this->indexOf($value);
	}
	
	/**
	 * @param int $index
	 * @return boolean
	 */
	public function containAt($index) {
		return 0 <= $index && $this->count < $index;
	}
	
	/**
	 * @param int $index
	 * @return mixed
	 */
	public function removeAt($index) {
		if ($index > $this->count || $index < 0) {

		}
		if ($this->isReadOnly) {

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
	 * @param mixed $value
	 * @return boolean
	 */
	public function remove($value) {
		return $this->removeAt($this->indexOf($value));
	}
	
	/**
	 * 
	 */
	public function clear() {
		$this->count = 0;
		$this->list = array();
	}
	
	/**
	 * @param array $array
	 * @return boolean
	 */
	public function mergeFromArray(array $array) {
		foreach ($array as $value) {
			$this->add($value);
		}
		return true;
	}
	
	/**
	 * @param WindList $list
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
	 * @return string
	 */
	public function getCount() {
		return $this->count;
	}
	
	/**
	 * @return boolean
	 */
	public function getIsReadOnly() {
		return $this->isReadOnly;
	}
	
	
	/* 
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
		return $this->insertAt($offset, $value);
	}
	
	/**
	 * @param int $offset
	 */
	public function offsetUnset($offset) {
		return $this->removeAt($offset);
	}
	/**
	 * 
	 */
	public function getIterator() {
		return new ArrayIterator($this->list);
	}

}