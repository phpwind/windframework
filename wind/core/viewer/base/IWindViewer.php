<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-15
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 视图引擎基类
 * 通过继承该方法可以实现对视图模板的调用解析
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
interface IWindViewer {
	
	/**
	 * 设置视图变量信息
	 * 
	 * @param array $vars
	 * @param string $key
	 */
	public function windAssign($vars, $key = '');
	
	/**
	 * 获取模板内容与变量信息
	 */
	public function windFetch($template = '');
	
	/**
	 * 获得一个视图信息，并初始化解析器
	 * 
	 * @param WindView $view
	 */
	public function initWithView($view);
	
	/**
	 * 获得一个Action操作句柄
	 * 
	 */
	public function doAction($actionHandle = '');

}