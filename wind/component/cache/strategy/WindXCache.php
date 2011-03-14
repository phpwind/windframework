<?php
/**
 * @author Su Qian <weihu@alibaba-inc.com> 2010-12-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:component.cache.AbstractWindCache');
L::import('WIND:component.cache.operator.WindUXCache');
/**
 * xcache加速缓存
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Su Qian <weihu@alibaba-inc.com>
 * @version $Id$ 
 * @package 
 */
class WindXCache extends AbstractWindCache {
	
	/**
	 * @var WindUXCache
	 */
	protected $xcache = null;
	
	public function __construct(){
		$this->xcache = new WindUXCache();
	}
	/* 
	 * @see AbstractWindCache#set()
	 */
	public function set($key, $value, $expire = 0, IWindCacheDependency $denpendency = null) {
		$expire = null === $expire  ? $this->getExpire() : $expire;
		return $this->xcache->set($this->buildSecurityKey($key), $this->storeData($value, $expire, $denpendency), $expire);
	}
	/* 
	 * @see AbstractWindCache#fetch()
	 */
	public function get($key) {
		return $this->getDataFromMeta($key, unserialize($this->xcache->get($this->buildSecurityKey($key))));
	}
	
	/* 
	 * @see AbstractWindCache#delete()
	 */
	public function delete($key) {
		return $this->xcache->delete($this->buildSecurityKey($key));
	}
	
	/* 
	 * @see AbstractWindCache#clear()
	 */
	public function clear() {
		return $this->xcache->flush();
	}
	/* 
	 * @see AbstractWindCache#getCacheHandler()
	 * @return WindUXCache
	 */
	public function getCacheHandler(){
		return $this->xcache;
	}
	
}