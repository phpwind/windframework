<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-12-30
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 类工厂接口定义
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
interface IWindFactory {

	/**
	 * 描述：根据类的别名获得一个类实例变量
	 * 返回：类的实例对象
	 * 
	 * 该方法首先通过类的别名到<b>类的配置文件中</b>找到类的相关配置信息，
	 * 加载类的路径并创建类的依赖
	 * 
	 * @param string $classAlias
	 */
	public function getInstance($classAlias);

	/**
	 * 根据类名称创建类对象
	 * 返回一个类类型的实例对象，通过此方法创建类实例，并不能自动获取类路径信息
	 * 
	 * @param string $className | 类名称
	 * @param array $args	| 类参数信息
	 * @return Object | 返回的类类型的实例对象
	 */
	public function createInstance($className, $args);

}