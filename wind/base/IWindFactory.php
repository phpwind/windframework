<?php
/**
 * 类工厂接口定义
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
interface IWindFactory {

	/**
	 * 类的实例对象
	 * 
	 * 通过调用该方法,获取类的实力变量
	 * @param string $classAlias
	 * @return instance
	 */
	public function getInstance($classAlias);

	/**
	 * 根据类名称创建类对象
	 * 返回一个类类型的实例对象，通过此方法创建类实例，并不能自动获取类路径信息
	 * 
	 * @param string $className 类名称
	 * @param array $args 类参数信息
	 * @return Object 返回的类类型的实例对象
	 */
	static public function createInstance($className, $args = array());
}