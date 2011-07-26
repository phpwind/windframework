<?php

Wind::import('COM:cache.AbstractWindCache');

/**
 * Eaccelerator是一款php加速器、优化器、编码器及动态内容缓存。
 * WindEaccelerator实现Eaccelerator动态内容缓存功能。
 * the last known user to change this file in the repository  <LastChangedBy: xiaoxiao >
 * @author xiaoxiao <x_824@sina.com>
 * @version 2011-7-26  xiaoxiao
 */
class WindEacceleratorCache extends AbstractWindCache {

	public function __construct() {
		if (!function_exists('eaccelerator_get')) {
			throw new WindException('The eaccelerator extension must be loaded !');
		}
	}

	/* 
	 * @see AbstractWindCache#setValue()
	 */
	protected function setValue($key, $value, $expire = 0) {
		return eaccelerator_put($key, $value, $expire);
	}

	/* 
	 * @see AbstractWindCache#get()
	 */
	protected function getValue($key) {
		return eaccelerator_get($key);
	}

	/* 
	 * @see AbstractWindCache#deleteValue()
	 */
	protected function deleteValue($key) {
		return eaccelerator_rm($key);
	}

	/* 
	 * @see AbstractWindCache#clear()
	 * @return boolean
	 */
	public function clear() {
		eaccelerator_gc();
		$cacheKeys = eaccelerator_list_keys();
		foreach ($cacheKeys as $key) {
			$this->delete(substr($key['name'], 1));
		}
	}
}