<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:component.cache.strategy.AbstractWindCache');
L::import('WIND:component.cache.operator.WindUWinCache');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */

class WindWinCache extends AbstractWindCache {
	/**
	 * @var WindUWinCache
	 */
	protected $wincache = null;
	
	public function __construct(){
		$this->wincache = new WindUWinCache();
	}
	/* 
	 * @see wind/component/cache/base/IWindCache#set()
	 */
	public function set($key, $value, $expires = 0, IWindCacheDependency $denpendency = null) {
		return $this->wincache->set($this->buildSecurityKey($key), $this->storeData($value, $expires, $denpendency), $expires);
	}
	/* 
	 * @see wind/component/cache/base/IWindCache#fetch()
	 */
	public function get($key) {
		return $this->getDataFromMeta($key, unserialize($this->wincache->get($this->buildSecurityKey($key))));
	}
	
	/* 
	 * @see wind/component/cache/base/IWindCache#delete()
	 */
	public function delete($key) {
		return $this->wincache->delete($this->buildSecurityKey($key));
	}
	
	/* 
	 * @see wind/component/cache/base/IWindCache#flush()
	 */
	public function flush() {
		return $this->windcache->flush();
	}
	
}