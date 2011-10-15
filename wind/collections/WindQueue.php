<?php
/**
 * 队列操作，先进先出
 * 
 * @author Qiong Wu <papa0924@gmail.com> 2011-10-8
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package collections
 */
class WindQueue implements IteratorAggregate,Countable{
	/**
	 * @var array 队列列表
	 */
	private $list = array();
	/**
	 * @var string 列表总数指示器
	 */
	private $count = 0;
	
	/**
	 * 移除并返回位于 Queue顶部的元素。
	 * @return mixed
	 */
	public function dequeue(){
		if(!$this->count){
			throw new WindException("The queue is empty");
		}
		--$this->count;
		return array_shift($this->list);
	}
	
	/**
	 * 返回位于 Queue顶部的对象但不将其移除。
	 * @return mixed
	 */
	public function peek(){
		if(!$this->count){
			throw new WindException("The queue is empty");
		}
		return $this->list[0];
	}
	
	/**
	 * 将元素添加到 Queue的底部。
	 * @param mixed $value
	 * @return number
	 */
	public function enqueue($value){
		++$this->count;
		return array_push($this->list,$value);
	}
	
	/**
	 * 确定某元素是否在 Queue中。
	 * @param mixed $value
	 * @return boolean
	 */
	public function contain($value){
		return false !==array_search($value, $this->list, true);
	}
	
	/**
	 * 将数组中的值合并到当前WindQueue队列
	 * @param array $array 要合并的数组
	 * @return boolean
	 */
	public function mergeFromArray(array $array) {
		foreach ($array as $value) {
			$this->enqueue($value);
		}
		return true;
	}
	/**
	 * 将WindQueue队列集合合并到当前WindQueue队列
	 * @param WindQueue $list 要合并的WindQueue集合
	 * @return boolean
	 */
	public function mergeFromQueue(WindQueue $queue) {
		foreach ($queue as $value) {
			$this->enqueue($value);
		}
		return true;
	}
	
	/**
	 *清空队列 
	 */
	public function clear(){
		$this->list = array();
		$this->count = 0;
		return true;
	}
	
	/**
	 * 创建 Queue的浅表副本。
	 * @return WindQueue
	 */
	public function __clone(){
		return new self();
	}
	
	/**
	 * 取得队列个数
	 * @return int
	 */
	public function getCount(){
		return $this->count;
	}
	
	
	/* 
	 * 计算队列个数
	 * @see Countable#count()
	 */
	public function count() {
		return $this->getCount();
	}

	/**
	 * 取得队列的迭代器
	 */
	public function getIterator() {
		return new ArrayIterator($this->list);
	}

	
}