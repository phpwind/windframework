<?php
/**
 * @author xiaoxiao <xiaoxia.xuxx@aliyun.com>  2011-7-19
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 * @package
 */
Wind::import('WIND:component.cache.AbstractWindCache');

class WindXCache extends AbstractWindCache {

	public function __construct() {
		if (!function_exists('xcache_get')) {
			throw new WindException('The xcache extension must be loaded !');
		}
	}

	/* 
	 * @see AbstractWindCache#setValue()
	 */
	protected function setValue($key, $value, $expire = 0) {
		return xcache_set($key, $value, $expire);
	}

	/* 
	 * @see AbstractWindCache#getValue()
	 */
	protected function getValue($key) {
		return xcache_get($key);
	}

	/* 
	 * @see AbstractWindCache#deleteValue()
	 */
	protected function deleteValue($key) {
		return xcache_unset($key);
	}

	/* 
	 * @see AbstractWindCache#clear()
	 */
	public function clear() {
		$max = xcache_count(XC_TYPE_VAR);
		for ($i = 0; $i < $max; $i++) {
			xcache_clear_cache(XC_TYPE_VAR, $i);
		}
		return true;
	}

}