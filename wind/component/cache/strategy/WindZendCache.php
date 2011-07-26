<?php
Wind::import('COM:cache.AbstractWindCache');
/**
 * 
 * the last known user to change this file in the repository  <LastChangedBy: xiaoxiao >
 * @author xiaoxiao <x_824@sina.com>
 * @version 2011-7-26  xiaoxiao
 */
class WindZendCache extends AbstractWindCache {

	public function __construct() {
		if (!function_exists('zend_shm_cache_fetch')) {
			throw new WindException('The zend cache extension must be loaded !');
		}
	}

	/* (non-PHPdoc)
	 * @see AbstractWindCache::setValue()
	 */
	protected function setValue($key, $value, $expire = 0) {
		return zend_shm_cache_store($key, $value, $expire);
	}

	/* (non-PHPdoc)
	 * @see AbstractWindCache::getValue()
	 */
	protected function getValue($key) {
		return zend_shm_cache_fetch($key);
	}

	/* (non-PHPdoc)
	 * @see AbstractWindCache::deleteValue()
	 */
	protected function deleteValue($key) {
		return zend_shm_cache_delete($key);
	}

	/* (non-PHPdoc)
	 * @see AbstractWindCache::clear()
	 */
	public function clear() {
		return zend_shm_cache_clear();
	}

}