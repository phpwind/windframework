<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

/**
 * 缓存依赖
 * 在依赖对象和被依赖对象对象之间建立一种有效的关联，当被依赖对象发生改变时，依赖对象就在缓存中被清楚
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
interface IWindCacheDependency {

	/**
	 * 注入依赖
	 * @param mixed
	 */
	public function injectDependent();

	/**
	 * 是否已更新
	 * @return boolean
	 */
	public function hasChanged();

	/**
	 * 获取依赖项的上次更改时间
	 * @return int
	 */
	public function getLastModified();
}