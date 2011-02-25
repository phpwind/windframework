<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */
L::import('WIND:component.cache.strategy.AbstractWindCache');
L::import('WIND:component.utility.WindUZendCache');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindZendCache extends AbstractWindCache {
	/**
	 * @var WindUZendCache
	 */
	protected $zendCache = null;
	
	public function __construct(){
		$this->zendCache = new WindUZendCache();
	}
	/* 
	 * @see wind/component/cache/base/IWindCache#set()
	 */
	public function set($key, $value, $expires = 0, IWindCacheDependency $denpendency = null) {
		return $this->zendCache->set($this->buildSecurityKey($key), $this->storeData($value, $expires, $denpendency), $expires);
	}
	/* 
	 * @see wind/component/cache/base/IWindCache#fetch()
	 */
	public function get($key) {
		return $this->getDataFromMeta($key, unserialize($this->zendCache->get($this->buildSecurityKey($key))));
	}
	
	/* 
	 * @see wind/component/cache/base/IWindCache#delete()
	 */
	public function delete($key) {
		return $this->zendCache->delete($this->buildSecurityKey($key));
	}
	
	/* 
	 * @see wind/component/cache/base/IWindCache#flush()
	 */
	public function flush() {
		return $this->zendCache->flush();
	}
	
}