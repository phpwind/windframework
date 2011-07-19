<?php
/**
 * 视图引擎基类
 * 通过继承该方法可以实现对视图模板的调用解析
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
interface IWindViewerResolver {

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

}