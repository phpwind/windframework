<?php
/**
 * @author xiaoxiao <xiaoxia.xuxx@aliyun.com>  2011-7-18
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 * @package
 */
Wind::import('WIND:component.cache.AbstractWindCache');
/**
 * Eaccelerator是一款php加速器、优化器、编码器及动态内容缓存。
 * WindEaccelerator实现Eaccelerator动态内容缓存功能。
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