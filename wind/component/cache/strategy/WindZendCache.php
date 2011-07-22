<?php
/**
 * @author xiaoxia.xu <xiaoxia.xuxx@alibaba-inc.com> 2011-07-21
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
Wind::import('WIND:component.cache.AbstractWindCache');

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