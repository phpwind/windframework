<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */
L::import('WIND:component.cache.dependency.WindCacheDependency');
/**
 * 文件缓存依赖实现
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindFileCacheDependency extends WindCacheDependency{
	

	public function __construct(){
		
	}
	
	protected function notifyDependencyChanged(){
		return null;
	}
}