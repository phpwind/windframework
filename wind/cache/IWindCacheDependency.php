<?php
/**
 * 缓存依赖基类
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$ 
 * @package 
 */
interface IWindCacheDependency {

	/**
	 * 初始化依赖设置
	 * 
	 * @param array $data  缓存策略中生成的格式良好的存储数组,包含有真是数据
	 */
	public abstract function injectDependent($data);

	/**
	 * 检查是否有变更
	 * 
	 * @param array $data 缓存策略中生成的格式良好的存储数组,包含有真是数据
	 * @return boolean 如果有变化则返回true,如果没有变化返回false
	 */
	public abstract function hasChanged($data);

}