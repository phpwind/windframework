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
class WindEaccelerator {
	public function __construct() {
		if (!function_exists('eaccelerator_get')) {
			throw new WindException('The eaccelerator extension must be loaded !');
		}
	}
	/**
	 * @param string $key 缓存键
	 * @param mixed $value 缓存键对应的值
	 * @param int $ttl 缓存的生命周期，单位是秒，省略该参数或指定为 0 表示不限时，直到服务器重启清空为止。
	 * @return boolean
	 */
	public function set($key, $value, $ttl = 0) {
		return eaccelerator_put($key, $value, $ttl);
	}
	
	/**
	 * 根据键移除缓存
	 * @param string $key 缓存的键
	 * @return mixed
	 */
	public function get($key) {
		return eaccelerator_get($key);
	}
	
	/**
	 * 删除缓存
	 * @param string $key
	 */
	public function delete($key) {
		return eaccelerator_rm($key);
	}
	
	/**
	 * 为 键加上锁定操作，以保证多进程多线程操作时数据的同步。
	 * @param string $key
	 */
	public function lock($key) {
		return eaccelerator_lock($key);
	}
	
	/**
	 * 来释放这个锁或等待程序请求结束时自动释放这个锁。
	 * @param string $key
	 * @return
	 */
	public function unlock($key) {
		return eaccelerator_unlock($key);
	}
	/**
	 * 将 $eval_code 代码的输出缓存 $ttl 秒
	 * @param string $key
	 * @param string $eval_code
	 * @param int $ttl
	 */
	public function cacheOutput($key, $eval_code, $ttl = 0) {
		return eaccelerator_cache_output($key, $eval_code, $ttl);
	}
	
	/**
	 * 将 $eval_code 代码的执行结果缓存 $ttl 秒
	 * @param string $key
	 * @param string $eval_code
	 * @param int $ttl
	 */
	public function cacheResult($key, $eval_code, $ttl = 0) {
		return eaccelerator_cache_result($key, $eval_code, $ttl);
	}
	
	/**
	 * 将当前整页缓存 $ttl 秒。
	 * @param string $key
	 * @param int $ttl
	 */
	public function pageCache($key, $ttl = 0) {
		return eaccelerator_cache_page($key, $ttl);
	}
	
	/**
	 * 删除由 eaccelerator_cache_page() 执行的缓存，
	 * @param string $key
	 */
	public function deletePageCache($key) {
		return eaccelerator_rm_page($key);
	}
	
	/**
	 * 移除清理所有已过期的key 
	 */
	public function clearExpiredCache() {
		return eaccelerator_gc();
	}
	
	/**
	 * 清空所有缓存
	 */
	public function flush() {
		$this->clearExpiredCache();
		$cacheKeys = eaccelerator_list_keys();
		foreach ($cacheKeys as $key) {
			$this->delete(substr($key['name'], 1));
		}
	}
}