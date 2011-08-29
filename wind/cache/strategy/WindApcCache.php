<?php

Wind::import('COM:cache.AbstractWindCache');
/**
 * 
 * the last known user to change this file in the repository  <LastChangedBy: xiaoxiao >
 * @author xiaoxiao <x_824@sina.com>
 * @version 2011-7-26  xiaoxiao
 */
class WindApcCache extends AbstractWindCache {
	
	public function __construct(){
		if (!extension_loaded('apc')) {
			throw new WindCacheException('The apc extension must be loaded !');
		}
	}
	
	/* 
	 * @see AbstractWindCache#setValue()
	 */
	protected function setValue($key, $value, $expires = 0) {
		return apc_store($key, $value, $expires);
	}
	
	/* 
	 * @see AbstractWindCache#getValue()
	 */
	protected function getValue($key) {
		return apc_fetch($key);
	}
	/* 
	 * @see AbstractWindCache#deleteValue()
	 */
	protected function deleteValue($key) {
		return apc_delete($key);
	}
	
	/*
	 * @see AbstractWindCache#clear()
	 */
	public function clear() {
		apc_clear_cache();
		return apc_clear_cache('user');
	}
}