<?php
/**
 * 缓存依赖基类
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package cache
 */
interface IWindCacheDependency {

	/**
	 * 初始化依赖设置
	 * 
	 * @param int $expires  缓存的过期时间
	 */
	public function injectDependent($expires);

	/**
	 * 检查是否有变更
	 * 
	 * @param AbstractWindCache $cache 缓存对象
	 * @param string $key 
	 * @param int $expires
	 * @return boolean 如果有变化则返回true,如果没有变化返回false
	 */
	public function hasChanged($cache, $key, $expires);

}