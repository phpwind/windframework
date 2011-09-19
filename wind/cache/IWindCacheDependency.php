<?php
/**
 * 缓存依赖基类
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
interface IWindCacheDependency {

	/**
	 * 初始化设置，设置依赖
	 */
	public abstract function injectDependent($data);

	/**
	 * 检查是否有变更
	 * 
	 * @return boolean
	 */
	public abstract function hasChanged($data);

}