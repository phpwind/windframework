<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 静态单利模式加载/在应用上下文中有状态存储
 * 继承了该基类的子类在类加载的过程中都会以静态单利方式存储
 * 通过调用
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
interface WContext {
	static public function getInstance();
}