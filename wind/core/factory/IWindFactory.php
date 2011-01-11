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
	 * 根据类的别名获得一个类实例变量
	 * 
	 * @param string $classAlias
	 */
	public function getInstance($classAlias);

	public function createInstance($className, $args);

}