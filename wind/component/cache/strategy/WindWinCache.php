<?php
/**
 * @author xiaoxia.xu <xiaoxia.xuxx@alibaba-inc.com> 2011-07-21
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
Wind::import('WIND:component.cache.AbstractWindCache');

class WindWinCache extends AbstractWindCache {
	
	public function __construct(){
		if (!function_exists('wincache_ucache_get')) {
			throw new WindException('The wincache extension must be loaded !');
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