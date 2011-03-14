<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Su Qian <weihu@alibaba-inc.com>
 * @version $Id$ 
 * @package 
 * tags
 */
L::import('WIND:component.cache.AbstractWindCache');
L::import('WIND:component.cache.operator.WindUZendCache');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Su Qian <weihu@alibaba-inc.com>
 * @version $Id$ 
 * @package 
 */
class WindZendCache extends AbstractWindCache {

	/**
	 * @var WindUZendCache
	 */
	protected $zendCache = null;

	public function __construct() {
		$this->zendCache = new WindUZendCache();
	}

	/* (non-PHPdoc)
	 * @see AbstractWindCache::set()
	 */
	public function set($key, $value, $expire = 0, IWindCacheDependency $denpendency = null) {
		$expire = null === $expire  ? $this->getExpire() : $expire;
		return $this->zendCache->set($this->buildSecurityKey($key), $this->storeData($value, $expire, $denpendency), $expire);
	}

	/* (non-PHPdoc)
	 * @see AbstractWindCache::get()
	 */
	public function get($key) {
		return $this->getDataFromMeta($key, unserialize($this->zendCache->get($this->buildSecurityKey($key))));
	}

	/* (non-PHPdoc)
	 * @see AbstractWindCache::delete()
	 */
	public function delete($key) {
		return $this->zendCache->delete($this->buildSecurityKey($key));
	}

	/**
	 * @see AbstractWindCache::clear()
	 * @return boolean
	 */
	public function clear() {
		return $this->zendCache->flush();
	}
	
	/* 
	 * @see AbstractWindCache#getCacheHandler()
	 * @return WindUZendCache
	 */
	public function getCacheHandler(){
		return $this->zendCache;
	}

}