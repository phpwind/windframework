<?php

Wind::import('WIND:cache.AbstractWindCache');
/**
 * 
 * the last known user to change this file in the repository  <LastChangedBy: xiaoxiao >
 * @author xiaoxiao <x_824@sina.com>
 * @version 2011-7-26  xiaoxiao
 */
class WindWinCache extends AbstractWindCache {

	public function __construct() {
		if (!function_exists('wincache_ucache_get')) {
			throw new WindCacheException('The wincache extension must be loaded !');
		}
	}

	/* 
	 * @see AbstractWindCache#setValue()
	 */
	protected function setValue($key, $value, $expire = 0) {
		return wincache_ucache_set($key, $value, $expire);
	}

	/* 
	 * @see AbstractWindCache#getValue()
	 */
	protected function getValue($key) {
		return wincache_ucache_get($key);
	}

	/* 
	 * @see AbstractWindCache#deleteValue()
	 */
	protected function deleteValue($key) {
		return wincache_ucache_delete($key);
	}

	/* 
	 * @see AbstractWindCache#clear()
	 */
	public function clear() {
		return wincache_ucache_clear();
	}

}