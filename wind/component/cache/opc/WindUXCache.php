<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */
class WindUXCache {
	
	public function __construct() {
		if (!function_exists('xcache_get')) {
			throw new WindException('The xcache extension must be loaded !');
		}
	}
	
	/**
	 * 设置缓存，如果缓存存在，则覆写，否则添加
	 * @param string $key 缓存键
	 * @param mixed $value 缓存键对应的值
	 * @param int $ttl 缓存的生命周期，单位是秒，省略该参数或指定为 0 表示不限时，直到服务器重启清空为止。
	 */
	public function set($key, $value, $ttl = 0) {
		return xcache_set($key, $value, $ttl);
	}
	
	/**
	 * 根据键移除缓存
	 * @param string $key 缓存的键
	 * @return mixed
	 */
	public function get($key) {
		return xcache_get($key);
	}
	/**
	 * 删除缓存
	 * @param string $key
	 */
	public function delete($key) {
		return xcache_unset($key);
	}
	
	/**
	 * 判断一个某个键对应的缓存是否存在
	 * @param string $key
	 */
	public function exist($key) {
		return xcache_isset($key);
	}
	/**
	 * 清空所有缓存
	 */
	public function flush() {
		for ($i = 0, $max = xcache_count(XC_TYPE_VAR); $i < $max; $i++) {
			xcache_clear_cache(XC_TYPE_VAR, $i);
		}
		return true;
	}
}
