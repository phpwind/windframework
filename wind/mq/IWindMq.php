<?php
/**
 * 消息队列接口定义
 * 
 * 定义了基础的消息队列接口。<code>
 * push  插入一条消息
 * pop   弹出一条消息，被弹出的值同时会在队列中删除
 * delete 删除一个队列
 * length 队列长度
 * range  取消息列表，offset默认为1，offset为0时取出全部消息
 * </code>
 * 消息队列中数据的存取形式是先进先出的，即先插入的输入先弹出。
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package mq
 */
interface IWindMq {

	/**
	 * 返回mq的对象
	 * 
	 * @return object
	 */
	public function getMq();

	/**
	 * 向消息队列中插入一条数据
	 *
	 * 向消息队列中插入一条数据,消息会被插入到队尾。
	 * 当插入失败时抛出异常
	 * @param string $key
	 * @param string $value 
	 * @throws WindMqException 
	 */
	public function push($key, $value);

	/**
	 * 从消息队列中弹出一条数据
	 * 
	 * 消息队列总是遵循先进先出的数据存取原则。
	 * 该接口将队列首部的数据弹出，并返回。
	 * @param string $key
	 * @return string
	 */
	public function pop($key);

	/**
	 * 返回名称为key的list中start开始，offset个元素（offset为 0 ，返回所有）
	 * 当$start为负数时，从对尾开始
	 * @param string $key
	 * @param int $start
	 * @param int $offset 
	 * @return array
	 */
	public function range($key, $start = 0, $offset = 1);

	/**
	 * 返回队列长度
	 *
	 * @param string $key
	 * @return int
	 */
	public function length($key);

	/**
	 * 删除队列key
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function delete($key);

}

?>