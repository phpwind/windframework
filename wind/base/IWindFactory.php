<?php
/**
 * 类工厂接口定义
 * 
 * 类工厂接口类主要有两个接口方法<i>getInstance,createInstance</i>.
 * 'getInstance'创建并返回类的实例对象,'createInstance'静态方法,用于创建类对象.
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package base
 */
interface IWindFactory {

	/**
	 * 创建并返回类的实例对象
	 * 
	 * 通过调用该方法,获取类的实例对象.当类的实例对象不存在时调用{@link createInstance}方法创建.
	 * 通过该方法创建类对象,需要确定该类的组件定义已经被加载.如果未被加载则返回一个null.
	 * @param string $classAlias 类别名 组件定义名称 必须填写
	 * @param array $args 参数列表
	 * @return instance 返回类实例对象
	 */
	public function getInstance($classAlias, $args = array());

	/**
	 * 创建并返回类对象
	 * 
	 * 返回一个类类型的实例对象，通过此方法创建类实例，并不能自动获取类路径信息
	 * @param string $className 类名称
	 * @param array $args 类参数信息
	 * @return Object 返回类的实例对象
	 */
	static public function createInstance($className, $args = array());
}