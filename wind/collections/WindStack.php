<?php
/**
 * 堆栈操作，先进后出
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package wind.collections
 */
class WindStack implements IteratorAggregate,Countable{
	
	/**
	 * @var array 集合列表
	 */
	private $list = array();
	/**
	 * @var string 列表总数指示器
	 */
	private $count = 0;
	
	/**
	 * 移除并返回位于 Stack 底部的对象。
	 * @return mixed
	 */
	public function pop(){
		if(!$this->count){
			throw new WindException("The stack is empty");
		}
		--$this->count;
		return array_pop($this->list);
	}
	
	/**
	 * 返回位于 Stack底部的对象但不将其移除。
	 * @return mixed
	 */
	public function peek(){
		if(!$this->count){
			throw new WindException("The stack is empty");
		}
		return $this->list[$this->count-1];
	}
	
	/**
	 * 确定某元素是否在 Stack中。
	 * @param mixed $value
	 * @return boolean
	 */
	public function contain($value){
		return false !== array_search($value, $this->list, true);
	}
	/**
	 * 将元素插入 Stack 的底部。
	 * @param mixed $value
	 * @return number
	 */
	public function push($value){
		++$this->count;
		return array_push($this->list,$value);
	}
	/**
	 * 清空队列
	 */
	public function clear(){
		$this->count = 0;
		$this->list = array();
		return true;
	}
    /**
	 * 将数组中的值合并到当前tack队列
	 * @param array $array 要合并的数组
	 * @return boolean
	 */
	public function mergeFromArray($array) {
		foreach ($array as $value) {
			$this->push($value);
		}
		return true;
	}
	/**
	 * 将WindStack堆栈集合合并到当前Stack队列
	 * @param WindStack $list 要合并的WindStack集合
	 * @return boolean
	 */
	public function mergeFromStack(WindStack  $stack) {
		foreach ($stack as $value) {
			$this->push($value);
		}
		return true;
	}
	/**
	 * 取得堆栈个数
	 * @return string
	 */
	public function getCount(){
		return $this->count;
	}
	/* 
	 * 计算堆栈的总个数
	 * @see Countable#count()
	 */
	public function count() {
		return $this->getCount();
	}
	/**
	 * 取得堆栈的迭代器
	 */
	public function getIterator() {
		return new ArrayIterator($this->list);
	}
	/**
	 * 创建 stack的浅表副本。
	 * @return WindStack
	 */
	public function __clone(){
		return new self();
	}
}