<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

/**
 * 缓存接口
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
interface IWindCache{

	/**
	 * 设置缓存，如果$key不存在，设置缓存，否则，抛出异常。
	 * @param string $key 保存缓存数据的键。
	 * @param string $value 缓存数据。
	 * @param string $expires 缓存数据的过期时间
	 * @param IWindCacheDependency $denpendency 缓存依赖
	 * @return boolean
	 */
	public function add($key,$value,$expires = 0,IWindCacheDependency $denpendency = null);
	/**
	 * 设置缓存，如果key不存在，设置缓存，否则，替换已有key的缓存。
	 * @param string $key 保存缓存数据的键。
	 * @param string $value 保存缓存数据。
	 * @param int $expires 缓存数据的过期时间
	 * @param IWindCacheDependency $denpendency 缓存依赖
	 * @return boolean
	 */
	public function set($key,$value,$expires = 0,IWindCacheDependency $denpendency = null);
	/**
	 * 替换缓存，如果key不存在，抛出异常，否则，替换缓存。
	 * @param string $key 保存缓存数据的键。
	 * @param string $value 缓存数据数据。
	 * @param int $expires 缓存数据的过期时间
 	 * @param IWindCacheDependency $denpendency 缓存依赖 
	 * @return boolean
	 */
	public function replace($key,$value,$expires = 0,IWindCacheDependency $denpendency = null);
	/**
	 * 获取指定缓存
	 * @param string $key 获取缓存数据的标识，即键
	 * @return string
	 */
	public function fetch($key);
	
	/**
	 * 通过key批量获取缓存数据
	 * @param array $keys
	 * @return array
	 */
	public function batchFetch($keys);
	/**
	 * 删除缓存数据
	 * @param string $key 获取缓存数据的标识，即键
	 * @return string
	 */
	public function delete($key);
	/**
	 * 通过key批量删除缓存数据
	 * @param array $keys
	 * @return array
	 */
	public function batchDelete($keys);
	/**
	 * 清空所有缓存
	 */
	public function flush();

}