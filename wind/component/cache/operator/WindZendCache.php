<?php
/**
 * @author Su Qian <weihu@alibaba-inc.om> 2010-12-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Su Qian <weihu@alibaba-inc.om>
 * @version $Id$ 
 * @package 
 */
class WindZendCache{
	
	public function __construct() {
		if (!function_exists('zend_shm_cache_fetch')) {
			throw new WindException('The zend cache extension must be loaded !');
		}
	}
	
	/**
	 * 设置缓存，如果缓存存在，则覆写，否则添加
	 * @param string $key 缓存键
	 * @param mixed $value 缓存键对应的值
	 * @param int $ttl 缓存的生命周期，单位是秒，省略该参数或指定为 0 表示不限时，直到服务器重启清空为止。
	 */
	public function set($key, $value, $ttl = 0) {
		return zend_shm_cache_store($key, $value, $ttl);
	}
	
	/**
	 * 根据键移除缓存
	 * @param string $key 缓存的键
	 * @return mixed
	 */
	public function get($key) {
		return zend_shm_cache_fetch($key);
	}
	/**
	 * 删除缓存
	 * @param string $key
	 */
	public function delete($key) {
		return zend_shm_cache_delete($key);
	}
	
	/**
	 * 清空所有缓存
	 */
	public function flush() {
		return zend_shm_cache_clear();
	}
}