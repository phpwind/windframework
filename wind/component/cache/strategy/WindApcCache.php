<?php
/**
 * @author xiaoxia.xu <xiaoxia.xuxx@alibaba-inc.com> 2011-07-21
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
Wind::import('WIND:component.cache.AbstractWindCache');

class WindApcCache extends AbstractWindCache {
	
	public function __construct(){
		if (!extension_loaded('apc')) {
			throw new WindException('The apc extension must be loaded !');
		}
	}
	
	/* 
	 * @see AbstractWindCache#addValue()
	 */
	protected function addValue($key, $value, $expires = 0) {
		return apc_store($key, $value, $ttl);
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